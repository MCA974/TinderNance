<?php

namespace App\Controller;

use App\DTO\CompanyProfileDto;
use App\Enum\EmployeeCountRange;
use App\Form\CompanyProfileType;
use App\Repository\CompanyProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        try {
            if ($id !== null) {
                $companyProfile = $this->companyProfileRepository->find($id);
            }

            if (!$companyProfile) {
                $companyProfile = $this->companyProfileRepository->findOneBy([]);
            }
        } catch (\Throwable) {
            $companyProfile = null;
        }

        // Initialisation du DTO avec les données existantes ou les valeurs de démo
        $dto = new CompanyProfileDto();
        if ($companyProfile) {
            $dto->companyName = $companyProfile->getCompanyName() ?? 'Acme Fintech';
            $dto->sector = $companyProfile->getSector() ?? 'Fintech / SaaS';
            $dto->shortPresentation = $companyProfile->getShortPresentation() ?? 'Nous construisons la prochaine génération d\'outils financiers pour les PME européennes.';
            $dto->employeeCount = $companyProfile->getEmployeeCount()?->value ?? EmployeeCountRange::RANGE_11_49->value;
            $dto->logoUrl = $companyProfile->getLogoUrl();
        } else {
            $dto->companyName = 'Acme Fintech';
            $dto->sector = 'Fintech / SaaS B2B';
            $dto->shortPresentation = 'Nous construisons la prochaine génération d\'outils financiers pour les PME européennes. Équipe de 30 personnes, full remote-friendly, stack moderne.';
            $dto->employeeCount = EmployeeCountRange::RANGE_11_49->value;
        }

        $form = $this->createForm(CompanyProfileType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($companyProfile) {
                $companyProfile->setCompanyName($dto->companyName);
                $companyProfile->setSector($dto->sector);
                $companyProfile->setShortPresentation($dto->shortPresentation);

                $enumCount = EmployeeCountRange::from($dto->employeeCount);
                $companyProfile->setEmployeeCount($enumCount);

                try {
                    $this->entityManager->persist($companyProfile);
                    $this->entityManager->flush();
                } catch (\Throwable) {
                    // Tolérance si la base n'est pas connectée
                }
            }

            $this->addFlash('success', 'Votre profil entreprise a été mis à jour ! 🎉');

            return $this->redirectToRoute('app_recruteur_profil');
        }

        // Données de démo pour le pipeline et le candidat en cours
        $demoCandidate = [
            'name' => 'Lucas Moreau',
            'school' => 'École 42 Paris',
            'schoolStatus' => 'Admis',
            'rhythm' => '3j Entr. / 2j CFA',
            'duration' => '24 mois (Sept 2025)',
            'location' => 'Boulogne-Billancourt',
            'bio' => '« Passionné de React, TypeScript et Node.js. J\'ai déjà déployé 2 projets complets en production, je suis autonome et très curieux des bonnes pratiques DevOps. »',
            'stack' => ['TypeScript', 'React.js', 'Node / NestJS', 'PostgreSQL', 'Docker', 'TailwindCSS'],
            'softSkills' => ['🤝 Esprit d\'équipe', '🔍 Rigueur & Tests', '🚀 Curiosité tech', '💬 Communication'],
            'projects' => [
                ['name' => 'PulseMetrics SaaS', 'tech' => 'Next.js 14, Tailwind, Prisma, Stripe'],
                ['name' => 'Microservices Engine', 'tech' => 'Architecture Node/Nest & RabbitMQ'],
            ],
            'activeStatus' => 'Candidat actif • Répond en < 2h',
            'hasCfaDossier' => true,
            'hasLikedOffer' => true,
        ];

        $demoPipeline = [
            'currentOffer' => 'Développeur Fullstack React / Node',
            'pendingCount' => 32,
            'matchesThisWeek' => 3,
        ];

        return $this->render('recruteur/profil.html.twig', [
            'companyProfile' => $companyProfile,
            'dto' => $dto,
            'form' => $form->createView(),
            'candidate' => $demoCandidate,
            'pipeline' => $demoPipeline,
        ]);
    }
}
