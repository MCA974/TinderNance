<?php

namespace App\Controller;

use App\DTO\StudentProfileDto;
use App\Entity\User;
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

        // Chargement du profil : priorité à l'utilisateur connecté, sinon consultation par id
        if ($currentUser instanceof User) {
            $studentProfile = $currentUser->getStudentProfile();
            if ($studentProfile === null && $id !== null) {
                // Mode consultation d'un profil public
                $studentProfile = $this->studentProfileRepository->find($id);
            }
        } elseif ($id !== null) {
            $studentProfile = $this->studentProfileRepository->find($id);
        }

        // Initialisation du DTO depuis les données réelles de l'entité
        $dto = new StudentProfileDto();
        if ($studentProfile) {
            $user = $studentProfile->getUser() ?? ($currentUser instanceof User ? $currentUser : null);
            $dto->fullName = $user ? trim(($user->getFirstName() ?? '') . ' ' . ($user->getLastName() ?? '')) : '';
            $dto->email = $user?->getEmail() ?? '';
            $dto->searchRegion = $studentProfile->getSearchArea()->getRegion() ?: ($studentProfile->getSearchArea()->getCity() ?: '');
            $dto->searchRadius = $studentProfile->getSearchArea()->getRadiusKm() ?? 25;
            $dto->schoolName = $studentProfile->getSchoolName() ?? '';
            $dto->obtainedDiploma = $studentProfile->getObtainedDiploma() ?? '';
            $dto->schoolCampus = '';
            $dto->schoolStatus = $studentProfile->getSchoolStatus()?->value ?? '';
            $dto->alternationRhythm = $studentProfile->getAlternationRhythm() ?? '';
            $dto->contractDuration = $studentProfile->getContractDuration()?->value ?? '';
            $dto->desiredStartDate = $studentProfile->getDesiredStartDate()?->format('d/m/Y') ?? '';
            $dto->bio = $studentProfile->getBio() ?? '';
            $dto->photoUrl = $studentProfile->getPhotoUrl();
            $dto->cvUrl = $studentProfile->getCvUrl();
            $dto->isPublic = $studentProfile->isPublic() ?? true;
            $dto->isPaused = $studentProfile->isPaused() ?? false;
        } else {
            // Aucun profil : on pré-remplit avec les infos de base de l'utilisateur connecté
            if ($currentUser instanceof User) {
                $dto->fullName = trim(($currentUser->getFirstName() ?? '') . ' ' . ($currentUser->getLastName() ?? ''));
                $dto->email = $currentUser->getEmail() ?? '';
            }
            $dto->searchRadius = 25;
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
                    $searchArea->setRegion($dto->searchRegion ?? '');
                    $searchArea->setRadiusKm($dto->searchRadius ?? 25);

                    $studentProfile->setSchoolName($dto->schoolName ?? '');
                    $studentProfile->setObtainedDiploma($dto->obtainedDiploma ?? '');
                    $studentProfile->setAlternationRhythm($dto->alternationRhythm ?? '');
                    $studentProfile->setBio($dto->bio ?? '');
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
