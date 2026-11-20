<?php

namespace App\Controller;

use App\DTO\StudentProfileDto;
use App\Entity\User;
use App\Form\StudentProfileType;
use App\Repository\StudentProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
    public function __construct(
        private readonly StudentProfileRepository $studentProfileRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface $slugger,
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
            $dto->isPublic = $studentProfile->isPublic() ?? false;
            $dto->isPaused = $studentProfile->isPaused() ?? false;
        } else {
            // Aucun profil : on pré-remplit avec les infos de base de l'utilisateur connecté
            if ($currentUser instanceof User) {
                $dto->fullName = trim(($currentUser->getFirstName() ?? '') . ' ' . ($currentUser->getLastName() ?? ''));
                $dto->email = $currentUser->getEmail() ?? '';
            }
            $dto->searchRadius = 25;
            $dto->isPublic = false;
            $dto->isPaused = false;
        }

        $form = $this->createForm(StudentProfileType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
                // isPaused vient du champ hidden : '1'/'0'/'true'/'false'
                $isPausedRaw = $form->get('isPaused')->getData();
                $studentProfile->setIsPaused(in_array($isPausedRaw, ['1', 'true', true], true));

                if ($dto->desiredStartDate) {
                    $date = \DateTimeImmutable::createFromFormat('d/m/Y', $dto->desiredStartDate);
                    if ($date) {
                        $studentProfile->setDesiredStartDate($date);
                    }
                }

                // --- Upload photo ---
                /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $photoFile */
                $photoFile = $dto->photoFile;
                if ($photoFile) {
                    $originalName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeName = $this->slugger->slug($originalName);
                    $newFilename = $safeName . '-' . uniqid() . '.' . $photoFile->guessExtension();
                    try {
                        $photoFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads/photos',
                            $newFilename
                        );
                        $studentProfile->setPhotoUrl('/uploads/photos/' . $newFilename);
                        $dto->photoUrl = '/uploads/photos/' . $newFilename;
                    } catch (FileException) {
                        $this->addFlash('warning', 'Impossible d\'enregistrer la photo.');
                    }
                }

                // --- Upload CV ---
                /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $cvFile */
                $cvFile = $dto->cvFile;
                if ($cvFile) {
                    $originalName = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeName = $this->slugger->slug($originalName);
                    $newFilename = $safeName . '-' . uniqid() . '.pdf';
                    try {
                        $cvFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads/cv',
                            $newFilename
                        );
                        $studentProfile->setCvUrl('/uploads/cv/' . $newFilename);
                        $dto->cvUrl = '/uploads/cv/' . $newFilename;
                    } catch (FileException) {
                        $this->addFlash('warning', 'Impossible d\'enregistrer le CV.');
                    }
                }

                try {
                    $this->entityManager->persist($studentProfile);
                    $this->entityManager->flush();
                } catch (\Throwable) {
                    $this->addFlash('danger', 'Erreur lors de la sauvegarde. Réessayez.');
                }

                $this->addFlash('success', 'Vos modifications ont été enregistrées avec succès ! 🎉');
            }

            return $this->redirectToRoute('app_profil_etudiant');
        }

        return $this->render('profil/etudiant.html.twig', [
            'studentProfile' => $studentProfile,
            'dto' => $dto,
            'form' => $form->createView(),
        ]);
    }
}
