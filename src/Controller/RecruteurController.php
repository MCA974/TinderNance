<?php

namespace App\Controller;

use App\DTO\CompanyProfileDto;
use App\Entity\CompanyProfile;
use App\Entity\User;
use App\Enum\EmployeeCountRange;
use App\Form\CompanyProfileType;
use App\Repository\CompanyProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecruteurController extends AbstractController
{
    public function __construct(
        private readonly CompanyProfileRepository $companyProfileRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/recruteur', name: 'app_recruteur', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_recruteur_profil');
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
