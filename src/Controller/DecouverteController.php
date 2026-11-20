<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use App\Service\DecouverteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/decouverte')]
class DecouverteController extends AbstractController
{
    public function __construct(
        private readonly DecouverteService $decouverteService,
    ) {
    }

    #[Route('', name: 'app_decouverte', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($currentUser->getRole() === UserRole::COMPANY) {
            return $this->redirectToRoute('app_recruteur_profil');
        }

        $studentProfile = $currentUser->getStudentProfile();
        if (!$studentProfile) {
            $this->addFlash('warning', 'Veuillez d\'abord compléter votre profil étudiant avant d\'accéder aux offres.');
            return $this->redirectToRoute('app_profil_etudiant');
        }

        $filters = [
            'rhythm' => $request->query->get('rhythm'),
            'studyLevel' => $request->query->get('studyLevel'),
            'duration' => $request->query->get('duration'),
        ];

        $offerCards = $this->decouverteService->getOffersForStudent($studentProfile, $filters);

        return $this->render('decouverte/index.html.twig', [
            'studentProfile' => $studentProfile,
            'offerCards' => $offerCards,
            'filters' => $filters,
        ]);
    }

    #[Route('/swipe/{id}', name: 'app_decouverte_swipe', methods: ['POST'])]
    public function swipe(int $id, Request $request): JsonResponse
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || !$currentUser->getStudentProfile()) {
            return new JsonResponse(['success' => false, 'error' => 'Non authentifié ou profil manquant.'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $action = $data['action'] ?? $request->request->get('action', 'LIKE');

        $result = $this->decouverteService->handleSwipe($currentUser->getStudentProfile(), $id, (string) $action);

        return new JsonResponse($result);
    }

    #[Route('/rewind', name: 'app_decouverte_rewind', methods: ['POST'])]
    public function rewind(): JsonResponse
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || !$currentUser->getStudentProfile()) {
            return new JsonResponse(['success' => false, 'error' => 'Non authentifié.'], Response::HTTP_UNAUTHORIZED);
        }

        $result = $this->decouverteService->rewindLastSwipe($currentUser->getStudentProfile());

        return new JsonResponse($result);
    }
}
