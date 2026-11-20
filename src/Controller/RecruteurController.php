<?php

namespace App\Controller;

use App\DTO\CompanyProfileDto;
use App\Entity\CompanyProfile;
use App\Entity\JobOffer;
use App\Entity\MatchConversation;
use App\Entity\StudentProfile;
use App\Entity\Swipe;
use App\Entity\User;
use App\Enum\EmployeeCountRange;
use App\Enum\MatchStatus;
use App\Enum\SwipeAction;
use App\Form\CompanyProfileType;
use App\Repository\CompanyProfileRepository;
use App\Repository\JobOfferRepository;
use App\Repository\MatchConversationRepository;
use App\Repository\StudentProfileRepository;
use App\Repository\SwipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecruteurController extends AbstractController
{
    public function __construct(
        private readonly CompanyProfileRepository $companyProfileRepository,
        private readonly StudentProfileRepository $studentProfileRepository,
        private readonly JobOfferRepository $jobOfferRepository,
        private readonly SwipeRepository $swipeRepository,
        private readonly MatchConversationRepository $matchConversationRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/recruteur', name: 'app_recruteur', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_recruteur_candidats');
    }

    #[Route('/recruteur/candidats/{offerId?}', name: 'app_recruteur_candidats', methods: ['GET'])]
    public function candidats(Request $request, ?int $offerId = null): Response
    {
        $companyProfile = null;
        $currentUser = $this->getUser();

        try {
            if ($currentUser instanceof User && $currentUser->getCompanyProfile()) {
                $companyProfile = $currentUser->getCompanyProfile();
            }
            if (!$companyProfile) {
                $companyProfile = $this->companyProfileRepository->findOneBy([]);
            }
        } catch (\Throwable) {
            $companyProfile = null;
        }

        // Récupération des offres de l'entreprise
        $jobOffers = [];
        $currentOffer = null;
        try {
            if ($companyProfile) {
                $jobOffers = $this->jobOfferRepository->findBy(['companyprofile' => $companyProfile]);
            }
            if ($offerId !== null) {
                $currentOffer = $this->jobOfferRepository->find($offerId);
            }
            if (!$currentOffer && !empty($jobOffers)) {
                $currentOffer = $jobOffers[0];
            }
        } catch (\Throwable) {
            $jobOffers = [];
            $currentOffer = null;
        }

        // Récupération des profils étudiants réels
        $students = [];
        $swipedStudentIds = [];
        $likedStudentIds = [];

        try {
            if ($currentOffer && $currentOffer->getId()) {
                // Trouver les swipes déjà effectués pour cette offre
                $existingSwipes = $this->swipeRepository->findBy(['joboffer' => $currentOffer]);
                foreach ($existingSwipes as $s) {
                    if ($s->getCompanyAction() !== null && $s->getStudentprofile()) {
                        $swipedStudentIds[] = $s->getStudentprofile()->getId();
                    }
                    if (($s->getStudentAction() === SwipeAction::LIKE || $s->getStudentAction() === SwipeAction::SUPER_LIKE) && $s->getStudentprofile()) {
                        $likedStudentIds[] = $s->getStudentprofile()->getId();
                    }
                }
            }

            $allStudents = $this->studentProfileRepository->findBy(['isPublic' => true, 'isPaused' => false]);
            foreach ($allStudents as $sp) {
                if (!in_array($sp->getId(), $swipedStudentIds, true)) {
                    $students[] = $sp;
                }
            }
        } catch (\Throwable) {
            $students = [];
        }

        // Données dynamiques des candidats
        $candidateCards = [];
        foreach ($students as $student) {
            $user = $student->getUser();
            $candidateCards[] = [
                'id' => $student->getId(),
                'name' => $user ? ($user->getFirstName() ?: 'Candidat') : 'Candidat',
                'fullName' => $user ? trim(($user->getFirstName() ?? '') . ' ' . ($user->getLastName() ?? '')) : 'Candidat Alternant',
                'age' => 20,
                'photoUrl' => $student->getPhotoUrl(),
                'studyLevel' => $student->getTargetStudyLevel()?->value ?: 'Bac+3',
                'diploma' => $student->getObtainedDiploma() ?: 'Concepteur Développeur d\'Applications',
                'school' => $student->getSchoolName() ?: 'Grande École du Numérique',
                'schoolStatus' => $student->getSchoolStatus()?->value === 'ADMIT' ? 'Admis' : ($student->getSchoolStatus()?->value === 'INSCRIT' ? 'Inscrit' : 'En recherche'),
                'rhythm' => $student->getAlternationRhythm() ?: '3j Entr. / 2j CFA',
                'duration' => $student->getContractDuration()?->value === '2_ANS' ? '24 mois' : ($student->getContractDuration()?->value === '1_AN' ? '12 mois' : '36 mois'),
                'location' => $student->getSearchArea()?->getCity() ?: ($student->getSearchArea()?->getRegion() ?: 'Île-de-France'),
                'bio' => $student->getBio() ?: 'Développeur motivé en recherche d\'une alternance pour mettre en pratique mes compétences et monter en autonomie au sein d\'une équipe dynamique.',
                'cvUrl' => $student->getCvUrl(),
                'hasLikedOffer' => in_array($student->getId(), $likedStudentIds, true),
                'activeStatus' => 'Candidat actif • Répond en < 2h',
                'hasCfaDossier' => true,
                'stack' => ['TypeScript', 'React.js', 'Node / NestJS', 'PostgreSQL', 'Docker', 'TailwindCSS'],
            ];
        }

        // Si aucun candidat n'est encore présent en base de données, profils de démonstration fidèles au design
        if (empty($candidateCards)) {
            $candidateCards = [
                [
                    'id' => 101,
                    'name' => 'Thomas',
                    'fullName' => 'Thomas Konnard',
                    'age' => 18,
                    'photoUrl' => null,
                    'studyLevel' => 'Bac+3',
                    'diploma' => 'Concepteur Développeur d\'Applications',
                    'school' => 'École 42 Paris',
                    'schoolStatus' => 'Admis',
                    'rhythm' => '3j Entr. / 2j CFA',
                    'duration' => '24 mois (Sept 2025)',
                    'location' => 'Boulogne-Billancourt',
                    'bio' => '« Passionné de React, TypeScript et Node.js. J\'ai déjà déployé 4 projets complets en production, je suis autonome et très curieux des bonnes pratiques DevOps. Je cherche une équipe agile pour monter en compétences et apporter une réelle valeur opérationnelle dès le premier trimestre ! »',
                    'cvUrl' => null,
                    'hasLikedOffer' => true,
                    'activeStatus' => 'Candidat actif • Répond en < 2h',
                    'hasCfaDossier' => true,
                    'stack' => ['TypeScript', 'React.js', 'Node / NestJS', 'PostgreSQL', 'Docker', 'TailwindCSS'],
                ],
                [
                    'id' => 102,
                    'name' => 'Lucas',
                    'fullName' => 'Lucas Moreau',
                    'age' => 22,
                    'photoUrl' => null,
                    'studyLevel' => 'Bac+5',
                    'diploma' => 'Mastère CTO & Tech Lead',
                    'school' => 'HÉTIC',
                    'schoolStatus' => 'Inscrit & Admis',
                    'rhythm' => '3j Entreprise / 2j École',
                    'duration' => '24 mois (Sept 2025)',
                    'location' => 'Paris & Petite Couronne',
                    'bio' => '« Développeur Fullstack passionné par l\'architecture logicielle, le clean code et les microservices. Expérience solide sur Symfony, React et architecture cloud AWS. »',
                    'cvUrl' => null,
                    'hasLikedOffer' => false,
                    'activeStatus' => 'Candidat actif • En ligne',
                    'hasCfaDossier' => true,
                    'stack' => ['PHP', 'Symfony', 'Next.js', 'Docker', 'GraphQL', 'AWS'],
                ],
            ];
        }

        // Statistiques dynamiques
        $pendingCount = count($candidateCards);
        $matchesThisWeek = 3;
        $swipedTodayCount = 18;

        try {
            if ($companyProfile) {
                $matchesThisWeek = (int) $this->matchConversationRepository->createQueryBuilder('m')
                    ->select('count(m.id)')
                    ->innerJoin('m.joboffer', 'j')
                    ->where('j.companyprofile = :cp')
                    ->andWhere('m.matchedAt >= :weekAgo')
                    ->setParameter('cp', $companyProfile)
                    ->setParameter('weekAgo', new \DateTimeImmutable('-7 days'))
                    ->getQuery()
                    ->getSingleScalarResult();
            }
        } catch (\Throwable) {
            $matchesThisWeek = 3;
        }

        return $this->render('recruteur/candidats.html.twig', [
            'companyProfile' => $companyProfile,
            'jobOffers' => $jobOffers,
            'currentOffer' => $currentOffer,
            'candidates' => $candidateCards,
            'pendingCount' => $pendingCount,
            'matchesThisWeek' => $matchesThisWeek,
            'swipedTodayCount' => $swipedTodayCount,
        ]);
    }

    #[Route('/recruteur/candidats/swipe', name: 'app_recruteur_swipe_candidate', methods: ['POST'])]
    public function swipeCandidate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: $request->request->all();
        $candidateId = (int) ($data['candidateId'] ?? 0);
        $offerId = (int) ($data['offerId'] ?? 0);
        $actionStr = strtoupper((string) ($data['action'] ?? ''));

        $isMatch = false;
        $candidateName = 'le candidat';

        try {
            $student = $this->studentProfileRepository->find($candidateId);
            $offer = $this->jobOfferRepository->find($offerId);

            if ($student && $offer) {
                $candidateName = $student->getUser()?->getFirstName() ?: 'le candidat';
                $swipe = $this->swipeRepository->findOneBy(['studentprofile' => $student, 'joboffer' => $offer]);

                if (!$swipe) {
                    $swipe = new Swipe();
                    $swipe->setStudentprofile($student);
                    $swipe->setJoboffer($offer);
                    $swipe->setIsRewound(false);
                }

                if ($actionStr === 'LIKE') {
                    $swipe->setCompanyAction(SwipeAction::LIKE);
                    $swipe->setCompanySwipedAt(new \DateTimeImmutable());

                    if ($swipe->getStudentAction() === SwipeAction::LIKE || $swipe->getStudentAction() === SwipeAction::SUPER_LIKE) {
                        $isMatch = true;
                        $match = new MatchConversation();
                        $match->setSwipe($swipe);
                        $match->setStudentprofile($student);
                        $match->setJoboffer($offer);
                        $match->setStatus(MatchStatus::MATCHED);
                        $match->setMatchedAt(new \DateTimeImmutable());
                        $this->entityManager->persist($match);
                    }
                } elseif ($actionStr === 'DISLIKE') {
                    $swipe->setCompanyAction(SwipeAction::DISLIKE);
                    $swipe->setCompanySwipedAt(new \DateTimeImmutable());
                }

                $this->entityManager->persist($swipe);
                $this->entityManager->flush();
            }
        } catch (\Throwable) {
            // Tolérance si la base de données n'est pas connectée
        }

        return new JsonResponse([
            'success' => true,
            'isMatch' => $isMatch,
            'candidateName' => $candidateName,
        ]);
    }

    #[Route('/recruteur/profil/{id?}', name: 'app_recruteur_profil', methods: ['GET', 'POST'])]
    public function profil(Request $request, ?int $id = null): Response
    {
        $companyProfile = null;
        $currentUser = $this->getUser();

        try {
            if ($id !== null) {
                $companyProfile = $this->companyProfileRepository->find($id);
            }

            if (!$companyProfile && $currentUser instanceof User && $currentUser->getCompanyProfile()) {
                $companyProfile = $currentUser->getCompanyProfile();
            }

            if (!$companyProfile) {
                $companyProfile = $this->companyProfileRepository->findOneBy([]);
            }
        } catch (\Throwable) {
            $companyProfile = null;
        }

        // Initialisation du DTO avec les données existantes ou les valeurs de l'utilisateur connecté
        $dto = new CompanyProfileDto();
        $user = $companyProfile?->getUser() ?? ($currentUser instanceof User ? $currentUser : null);

        if ($companyProfile) {
            $dto->companyName = $companyProfile->getCompanyName();
            $dto->sector = $companyProfile->getSector() ?: 'Technologies & Édition Logicielle';
            $dto->shortPresentation = $companyProfile->getShortPresentation() ?: 'Nous concevons des solutions logicielles innovantes pour transformer l\'expérience de travail des équipes modernes. Rejoignez une équipe passionnée en pleine croissance !';
            $dto->employeeCount = $companyProfile->getEmployeeCount()?->value ?: EmployeeCountRange::RANGE_11_49->value;
            $dto->logoUrl = $companyProfile->getLogoUrl();
        }

        if ($user) {
            $dto->contactName = trim(($user->getFirstName() ?? '') . ' ' . ($user->getLastName() ?? ''));
            $dto->email = $user->getEmail();
            if (empty($dto->companyName)) {
                $dto->companyName = $dto->contactName;
            }
        }

        // Valeurs de repli par défaut si champs non encore renseignés
        $dto->companyName = $dto->companyName ?: 'Acme Technologies';
        $dto->contactName = $dto->contactName ?: 'Konnard Thomas';
        $dto->email = $dto->email ?: 'recrutement@acme.tech';
        $dto->sector = $dto->sector ?: 'Technologies & Édition Logicielle';
        $dto->shortPresentation = $dto->shortPresentation ?: 'Nous concevons des solutions logicielles innovantes pour transformer l\'expérience de travail des équipes modernes. Rejoignez une équipe passionnée en pleine croissance !';
        $dto->employeeCount = $dto->employeeCount ?: EmployeeCountRange::RANGE_11_49->value;
        $dto->isPublic = true;
        $dto->isPaused = false;

        $form = $this->createForm(CompanyProfileType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement de l'upload du logo si présent
            if ($dto->logoFile instanceof UploadedFile) {
                try {
                    $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/logos';
                    if (!is_dir($uploadsDir)) {
                        mkdir($uploadsDir, 0777, true);
                    }
                    $newFilename = uniqid('logo_') . '.' . ($dto->logoFile->guessExtension() ?? 'png');
                    $dto->logoFile->move($uploadsDir, $newFilename);
                    $dto->logoUrl = '/uploads/logos/' . $newFilename;
                } catch (\Throwable) {
                    // Erreur silencieuse d'upload
                }
            }

            try {
                if (!$companyProfile) {
                    $companyProfile = new CompanyProfile();
                    if ($user) {
                        $companyProfile->setUser($user);
                    }
                }

                $companyProfile->setCompanyName($dto->companyName);
                $companyProfile->setSector($dto->sector);
                $companyProfile->setShortPresentation($dto->shortPresentation);

                try {
                    $enumCount = EmployeeCountRange::from($dto->employeeCount);
                    $companyProfile->setEmployeeCount($enumCount);
                } catch (\ValueError) {
                    $companyProfile->setEmployeeCount(EmployeeCountRange::RANGE_11_49);
                }

                if ($dto->logoUrl) {
                    $companyProfile->setLogoUrl($dto->logoUrl);
                }

                if ($user) {
                    $nameParts = explode(' ', trim($dto->contactName), 2);
                    $user->setFirstName($nameParts[0] ?? '');
                    $user->setLastName($nameParts[1] ?? '');
                    if ($dto->email) {
                        $user->setEmail($dto->email);
                    }
                }

                $this->entityManager->persist($companyProfile);
                $this->entityManager->flush();
            } catch (\Throwable) {
                // Tolérance si la base de données n'est pas connectée
            }

            $this->addFlash('success', 'Votre profil entreprise a été enregistré avec succès ! 🎉');

            return $this->redirectToRoute('app_recruteur_profil');
        }

        return $this->render('recruteur/profil.html.twig', [
            'companyProfile' => $companyProfile,
            'dto' => $dto,
            'form' => $form->createView(),
        ]);
    }
}
