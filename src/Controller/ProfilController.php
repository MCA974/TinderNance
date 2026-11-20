<?php

namespace App\Controller;

use App\Repository\StudentProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profil')]
class ProfilController extends AbstractController
{
    public function __construct(
        private readonly StudentProfileRepository $studentProfileRepository,
    ) {
    }

    #[Route('/etudiant/{id?}', name: 'app_profil_etudiant', methods: ['GET'])]
    public function etudiant(?int $id = null): Response
    {
        $studentProfile = null;
        if ($id !== null) {
            $studentProfile = $this->studentProfileRepository->find($id);
        }

        if (!$studentProfile) {
            $studentProfile = $this->studentProfileRepository->findOneBy([]);
        }

        return $this->render('profil/etudiant.html.twig', [
            'studentProfile' => $studentProfile,
        ]);
    }
}
