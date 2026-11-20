<?php

namespace App\Controller;

use App\Repository\StudentProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfilController extends AbstractController
{
    public function __construct(
        private readonly StudentProfileRepository $studentProfileRepository,
    ) {
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->redirectToRoute('app_profil_etudiant');
    }

    #[Route('/profil/etudiant/{id?}', name: 'app_profil_etudiant', methods: ['GET'])]
    public function etudiant(?int $id = null): Response
    {
        $studentProfile = null;

        try {
            if ($id !== null) {
                $studentProfile = $this->studentProfileRepository->find($id);
            }

            if (!$studentProfile) {
                $studentProfile = $this->studentProfileRepository->findOneBy([]);
            }
        } catch (\Throwable) {
            // En cas d'indisponibilité de la base de données, on conserve $studentProfile à null
            // pour permettre le rendu visuel avec les données de démonstration du template.
            $studentProfile = null;
        }

        return $this->render('profil/etudiant.html.twig', [
            'studentProfile' => $studentProfile,
        ]);
    }
}
