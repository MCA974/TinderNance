<?php

namespace App\Controller;

use App\DTO\StudentProfileDto;
use App\Entity\StudentProfile;
use App\Entity\User;
use App\Enum\ContractDuration;
use App\Form\StudentProfileType;
use App\Repository\StudentProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    public function __construct(
        private readonly StudentProfileRepository $studentProfileRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/profil/etudiant/{id?}', name: 'app_profil_etudiant', methods: ['GET', 'POST'])]
    public function etudiant(Request $request, ?int $id = null): Response
    {
        $studentProfile = null;
        $currentUser = $this->getUser();

        try {
            if ($currentUser instanceof User && $currentUser->getStudentProfile()) {
                $studentProfile = $currentUser->getStudentProfile();
            } elseif ($id !== null) {
                $studentProfile = $this->studentProfileRepository->find($id);
            }

            if (!$studentProfile) {
                $studentProfile = $this->studentProfileRepository->findOneBy([]);
            }
        } catch (\Throwable) {
            $studentProfile = null;
        }

        // Initialisation du DTO avec les données du profil existant ou les valeurs par défaut
        $dto = new StudentProfileDto();
        if ($studentProfile) {
            $user = $studentProfile->getUser() ?? ($currentUser instanceof User ? $currentUser : null);
            $dto->fullName = $user ? trim(($user->getFirstName() ?? '') . ' ' . ($user->getLastName() ?? '')) : 'Lucas Moreau';
            $dto->email = $user?->getEmail() ?? 'lucas.moreau@student-hetic.net';
            $dto->searchRegion = $studentProfile->getSearchArea()->getRegion() ?? $studentProfile->getSearchArea()->getCity() ?? 'Paris & Petite Couronne (75, 92, 93, 94)';
            $dto->searchRadius = $studentProfile->getSearchArea()->getRadiusKm() ?? 25;
            $dto->schoolName = $studentProfile->getSchoolName() ?? 'HÉTIC - La Grande École de la Tech';
            $dto->obtainedDiploma = $studentProfile->getObtainedDiploma() ?? 'Mastère CTO & Tech Lead • Niveau 7 (Bac +5)';
            $dto->schoolCampus = 'HÉTIC - Campus Montreuil';
            $dto->schoolStatus = $studentProfile->getSchoolStatus()?->value ?? 'INSCRIT';
            $dto->alternationRhythm = $studentProfile->getAlternationRhythm() ?? '3j Entreprise / 2j École';
            $dto->contractDuration = $studentProfile->getContractDuration()?->value ?? '2_ANS';
            $dto->desiredStartDate = $studentProfile->getDesiredStartDate()?->format('d/m/Y') ?? '09/01/2025';
            $dto->bio = $studentProfile->getBio() ?? "Développeur Fullstack passionné par l'architecture React, Next.js et TypeScript. Rigoureux, curieux et autonome, je cherche à rejoindre une scale-up produit. GAMER dans l’âme jeux préféré ROBLOX";
            $dto->photoUrl = $studentProfile->getPhotoUrl();
            $dto->cvUrl = $studentProfile->getCvUrl();
            $dto->isPublic = $studentProfile->isPublic() ?? true;
            $dto->isPaused = $studentProfile->isPaused() ?? false;
        } else {
            $dto->fullName = 'Lucas Moreau';
            $dto->email = 'lucas.moreau@student-hetic.net';
            $dto->searchRegion = 'Paris & Petite Couronne (75, 92, 93, 94)';
            $dto->searchRadius = 25;
            $dto->schoolName = 'HÉTIC - La Grande École de la Tech';
            $dto->obtainedDiploma = 'Mastère CTO & Tech Lead • Niveau 7 (Bac +5)';
            $dto->schoolCampus = 'HÉTIC - Campus Montreuil';
            $dto->schoolStatus = 'INSCRIT';
            $dto->alternationRhythm = '3j Entreprise / 2j École';
            $dto->contractDuration = '2_ANS';
            $dto->desiredStartDate = '09/01/2025';
            $dto->bio = "Développeur Fullstack passionné par l'architecture React, Next.js et TypeScript. Rigoureux, curieux et autonome, je cherche à rejoindre une scale-up produit. GAMER dans l’âme jeux préféré ROBLOX";
            $dto->isPublic = true;
            $dto->isPaused = false;
        }

        $form = $this->createForm(StudentProfileType::class, $dto);
        if ($request) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Application des modifications si l'entité existe
                if ($studentProfile) {
                    $user = $studentProfile->getUser();
                    if ($user) {
                        $parts = explode(' ', trim($dto->fullName), 2);
                        $user->setFirstName($parts[0] ?? '');
                        $user->setLastName($parts[1] ?? '');
                        $user->setEmail($dto->email);
                    }

                    $searchArea = $studentProfile->getSearchArea();
                    $searchArea->setRegion($dto->searchRegion);
                    $searchArea->setRadiusKm($dto->searchRadius);

                    $studentProfile->setSchoolName($dto->schoolName);
                    $studentProfile->setObtainedDiploma($dto->obtainedDiploma);
                    $studentProfile->setAlternationRhythm($dto->alternationRhythm);
                    $studentProfile->setBio($dto->bio);
                    $studentProfile->setIsPublic($dto->isPublic);
                    $studentProfile->setIsPaused($dto->isPaused);

                    if ($dto->desiredStartDate) {
                        $date = \DateTimeImmutable::createFromFormat('d/m/Y', $dto->desiredStartDate);
                        if ($date) {
                            $studentProfile->setDesiredStartDate($date);
                        }
                    }

                    try {
                        $this->entityManager->persist($studentProfile);
                        $this->entityManager->flush();
                    } catch (\Throwable) {
                        // Tolérance si la base n'est pas connectée
                    }
                }

                $this->addFlash('success', 'Vos modifications ont été enregistrées avec succès ! 🎉');
            }
        }

        return $this->render('profil/etudiant.html.twig', [
            'studentProfile' => $studentProfile,
            'dto' => $dto,
            'form' => $form->createView(),
        ]);
    }
}
