<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user instanceof User) {
            if ($user->getRole() === UserRole::COMPANY) {
                return $this->redirectToRoute('app_recruteur_profil');
            }

            if ($user->getRole() === UserRole::STUDENT) {
                return $this->redirectToRoute('app_profil_etudiant');
            }
        }

        return $this->render('home/index.html.twig', [
            'user' => $user,
        ]);
    }
}
