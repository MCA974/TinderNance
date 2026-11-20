<?php

namespace App\Service;

use App\Entity\CompanyProfile;
use App\Entity\JobOffer;
use App\Entity\MatchConversation;
use App\Entity\StudentProfile;
use App\Entity\Swipe;
use App\Enum\ContractDuration;
use App\Enum\EmployeeCountRange;
use App\Enum\MatchStatus;
use App\Enum\OfferStatus;
use App\Enum\StudyLevel;
use App\Enum\SwipeAction;
use App\Repository\CompanyProfileRepository;
use App\Repository\JobOfferRepository;
use App\Repository\SwipeRepository;
use Doctrine\ORM\EntityManagerInterface;

class DecouverteService
{
    public function __construct(
        private readonly JobOfferRepository $jobOfferRepository,
        private readonly SwipeRepository $swipeRepository,
        private readonly CompanyProfileRepository $companyProfileRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Récupère la liste des offres disponibles pour un étudiant, avec calcul du score de match.
     *
     * @return array<int, array{offer: JobOffer, matchScore: int, formattedStudyLevels: string, formattedDurations: string}>
     */
    public function getOffersForStudent(StudentProfile $studentProfile, array $filters = []): array
    {
        $this->ensureOffersExist();

        $rhythm = $filters['rhythm'] ?? null;
        $studyLevel = $filters['studyLevel'] ?? null;
        $duration = $filters['duration'] ?? null;

        $offers = $this->jobOfferRepository->findAvailableOffersForStudent(
            $studentProfile,
            $rhythm,
            $studyLevel,
            $duration
        );

        $results = [];
        foreach ($offers as $offer) {
            $matchScore = $this->calculateMatchScore($studentProfile, $offer);

            $studyLabels = array_map(fn($lvl) => is_object($lvl) ? $lvl->value : (string) $lvl, $offer->getRequiredStudyLevel());
            $durationLabels = array_map(function($dur) {
                $val = is_object($dur) ? $dur->value : (string) $dur;
                return match($val) {
                    '1_AN' => '12 mois',
                    '2_ANS' => '24 mois',
                    '3_ANS' => '36 mois',
                    '6_MOIS' => '6 mois',
                    default => $val,
                };
            }, $offer->getAcceptedDurations());

            $results[] = [
                'offer' => $offer,
                'matchScore' => $matchScore,
                'formattedStudyLevels' => implode(' / ', $studyLabels),
                'formattedDurations' => implode(', ', $durationLabels),
            ];
        }

        return $results;
    }

    /**
     * Traite un swipe (LIKE, DISLIKE, SUPER_LIKE) effectué par l'étudiant.
     */
    public function handleSwipe(StudentProfile $studentProfile, int $jobOfferId, string $actionName): array
    {
        $action = SwipeAction::tryFrom(strtoupper($actionName));
        if (!$action) {
            return ['success' => false, 'error' => 'Action de swipe invalide.'];
        }

        $jobOffer = $this->jobOfferRepository->find($jobOfferId);
        if (!$jobOffer) {
            return ['success' => false, 'error' => 'Offre introuvable.'];
        }

        $swipe = $this->swipeRepository->findExistingSwipe($studentProfile, $jobOffer);
        if (!$swipe) {
            $swipe = new Swipe();
            $swipe->setStudentprofile($studentProfile);
            $swipe->setJoboffer($jobOffer);
        }

        $swipe->setStudentAction($action);
        $swipe->setStudentSwipedAt(new \DateTimeImmutable());
        $swipe->setIsRewound(false);

        // Simulation : Si super-like ou like sur l'offre d'une entreprise partenaire, l'entreprise like en retour
        $isMatch = ($action === SwipeAction::SUPER_LIKE) || ($action === SwipeAction::LIKE && ($jobOffer->getId() % 2 === 1));
        $companyAction = $isMatch ? SwipeAction::LIKE : SwipeAction::DISLIKE;
        $swipe->setCompanyAction($companyAction);
        $swipe->setCompanySwipedAt(new \DateTimeImmutable());

        $this->entityManager->persist($swipe);

        // Création du match si intérêt mutuel
        $match = $swipe->getMatchConversation();
        if ($isMatch) {
            if (!$match) {
                $match = new MatchConversation();
                $match->setStatus(MatchStatus::ACTIVE);
                $match->setMatchedAt(new \DateTimeImmutable());
                $match->setSwipe($swipe);
                $match->setStudentprofile($studentProfile);
                $match->setJoboffer($jobOffer);
                $swipe->setMatchConversation($match);
                $this->entityManager->persist($match);
            } else {
                $match->setStatus(MatchStatus::ACTIVE);
            }
        } elseif ($match) {
            $match->setStatus(MatchStatus::UNMATCHED);
        }

        $this->entityManager->flush();

        $company = $jobOffer->getCompanyprofile();
        return [
            'success' => true,
            'isMatch' => $isMatch,
            'companyName' => $company ? $company->getCompanyName() : 'Entreprise',
            'companyLogo' => $company ? $company->getLogoUrl() : null,
            'offerTitle' => $jobOffer->getTitle(),
        ];
    }

    /**
     * Annule le dernier swipe actif effectué par l'étudiant.
     */
    public function rewindLastSwipe(StudentProfile $studentProfile): array
    {
        $lastSwipe = $this->swipeRepository->findLatestStudentSwipe($studentProfile);
        if (!$lastSwipe) {
            return ['success' => false, 'message' => 'Aucun swipe à annuler.'];
        }

        $lastSwipe->setIsRewound(true);

        if ($lastSwipe->getMatchConversation()) {
            $lastSwipe->getMatchConversation()->setStatus(MatchStatus::UNMATCHED);
        }

        $this->entityManager->flush();

        return [
            'success' => true,
            'offerId' => $lastSwipe->getJoboffer()?->getId(),
        ];
    }

    /**
     * Calcule un score d'affinité en pourcentage entre le profil étudiant et l'offre d'alternance.
     */
    private function calculateMatchScore(StudentProfile $student, JobOffer $offer): int
    {
        $score = 70; // Score de base élevé pour valoriser les recommandations

        // Même rythme de formation
        if ($student->getAlternationRhythm() && $offer->getAcceptedRhythm()) {
            if (stripos($student->getAlternationRhythm(), $offer->getAcceptedRhythm()) !== false
                || stripos($offer->getAcceptedRhythm(), $student->getAlternationRhythm()) !== false) {
                $score += 15;
            }
        }

        // Même niveau d'études
        if ($student->getTargetStudyLevel() && !empty($offer->getRequiredStudyLevel())) {
            foreach ($offer->getRequiredStudyLevel() as $reqLevel) {
                if ($reqLevel === $student->getTargetStudyLevel()) {
                    $score += 10;
                    break;
                }
            }
        }

        // Même durée de contrat
        if ($student->getContractDuration() && !empty($offer->getAcceptedDurations())) {
            foreach ($offer->getAcceptedDurations() as $reqDur) {
                if ($reqDur === $student->getContractDuration()) {
                    $score += 5;
                    break;
                }
            }
        }

        return min(99, max(60, $score));
    }

    /**
     * Initialise quelques offres réalistes (basées sur la maquette Figma) si la base est vide.
     */
    private function ensureOffersExist(): void
    {
        if ($this->jobOfferRepository->count([]) > 0) {
            return;
        }

        $company = $this->companyProfileRepository->findOneBy([]);
        if (!$company) {
            return;
        }

        // Compléter les données de l'entreprise si nécessaire
        if (!$company->getSector()) {
            $company->setSector('Fintech & SaaS RH');
        }
        if (!$company->getCompanyName() || $company->getCompanyName() === 'Entreprise Dupont') {
            $company->setCompanyName('PayFit France');
        }
        if (!$company->getLogoUrl()) {
            $company->setLogoUrl('/images/offers/banner_default.png');
        }
        if (empty($company->getMediaGallery())) {
            $company->setMediaGallery([
                '/images/offers/office_1.png',
                '/images/offers/office_2.png',
                '/images/offers/office_3.png',
            ]);
        }

        // Offre 1 : Correspondant fidèlement à la maquette Figma
        $offer1 = new JobOffer();
        $offer1->setTitle('Développeur Fullstack React / Node.js');
        $offer1->setMissionDescription("Au sein de la squad Payroll, vous participez à la refonte de nos moteurs de calcul déclaratif social en TypeScript, NestJS et React 18 avec GraphQL. Un Lead Dev senior vous est assigné comme tuteur permanent pour une progression rapide.");
        $offer1->setExactAdress('Paris 9e • Grands Boulevards');
        $offer1->setTutorName('Alexandre Martin (Lead Dev)');
        $offer1->setWebsiteUrl('https://payfit.com');
        $offer1->setRequiredStudyLevel([StudyLevel::BAC_PLUS_4, StudyLevel::BAC_PLUS_5]);
        $offer1->setAcceptedDurations([ContractDuration::TWO_YEARS]);
        $offer1->setAcceptedRhythm('3j Entreprise / 2j École');
        $offer1->setStartDate(new \DateTimeImmutable('+3 months'));
        $offer1->setPerks([
            'Matériel MacBook Pro M3',
            'Télétravail 2 jours / semaine',
            'Carte Swile 11,50 € (60% prise en charge)',
            'Salle de sport & Rooftop privatif',
            'Équipe jeune (moyenne 28 ans)',
        ]);
        $offer1->setStatus(OfferStatus::ACTIVE);
        $offer1->setCreatedAt(new \DateTimeImmutable('-2 days'));
        $offer1->setCompanyprofile($company);
        $this->entityManager->persist($offer1);

        // Offre 2
        $offer2 = new JobOffer();
        $offer2->setTitle('Développeur Web Symfony & Vue.js');
        $offer2->setMissionDescription("Sous la responsabilité de notre CTO, vous rejoindrez le pôle Core API pour concevoir de nouvelles fonctionnalités en Symfony 8.1 et composants Vue.js 3 interactifs dans une architecture moderne découplée.");
        $offer2->setExactAdress('Paris 11e • Bastille');
        $offer2->setTutorName('Sophie Bernard (Engineering Manager)');
        $offer2->setWebsiteUrl('https://tech-innov.io');
        $offer2->setRequiredStudyLevel([StudyLevel::BAC_PLUS_3, StudyLevel::BAC_PLUS_4]);
        $offer2->setAcceptedDurations([ContractDuration::ONE_YEAR, ContractDuration::TWO_YEARS]);
        $offer2->setAcceptedRhythm('1 semaine CFA / 3 sem. Entreprise');
        $offer2->setStartDate(new \DateTimeImmutable('+2 months'));
        $offer2->setPerks([
            'Télétravail flexible',
            'Carte restaurant 10 €',
            'Mutuelle Alan 100% prise en charge',
            'Budget formation annuel',
        ]);
        $offer2->setStatus(OfferStatus::ACTIVE);
        $offer2->setCreatedAt(new \DateTimeImmutable('-5 days'));
        $offer2->setCompanyprofile($company);
        $this->entityManager->persist($offer2);

        // Offre 3
        $offer3 = new JobOffer();
        $offer3->setTitle('Ingénieur DevOps & Cloud AWS Junior');
        $offer3->setMissionDescription("Intégrez notre squad Infrastructure & Platform. Vous serez formé à l'orchestration Docker & Kubernetes, aux pipelines CI/CD GitHub Actions et à l'automatisation Terraform sur AWS.");
        $offer3->setExactAdress('Boulogne-Billancourt (92)');
        $offer3->setTutorName('Julien Dubois (DevOps Architect)');
        $offer3->setWebsiteUrl('https://cloud-scale.com');
        $offer3->setRequiredStudyLevel([StudyLevel::BAC_PLUS_4, StudyLevel::BAC_PLUS_5]);
        $offer3->setAcceptedDurations([ContractDuration::TWO_YEARS]);
        $offer3->setAcceptedRhythm('3j Entreprise / 2j École');
        $offer3->setStartDate(new \DateTimeImmutable('+4 months'));
        $offer3->setPerks([
            'Poste Linux / Mac au choix',
            '100% Télétravail possible',
            'Accès illimité aux certifications AWS',
            'Événements tech & Hackathons',
        ]);
        $offer3->setStatus(OfferStatus::ACTIVE);
        $offer3->setCreatedAt(new \DateTimeImmutable('-1 week'));
        $offer3->setCompanyprofile($company);
        $this->entityManager->persist($offer3);

        $this->entityManager->flush();
    }
}
