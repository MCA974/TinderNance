<?php

namespace App\Controller;

use App\Entity\CompanyProfile;
use App\Entity\StudentProfile;
use App\Entity\User;
use App\Enum\UserRole;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        Security $security,
    ): Response {
        $currentUser = $this->getUser();
        if ($currentUser instanceof User) {
            if ($currentUser->getRole() === UserRole::COMPANY) {
                return $this->redirectToRoute('app_recruteur_profil');
            }
            if ($currentUser->getRole() === UserRole::STUDENT) {
                return $this->redirectToRoute('app_profil_etudiant');
            }

            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        // Default to student if not selected
        $user->setRole(UserRole::STUDENT);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Set creation date
            $user->setCreatedAt(new \DateTimeImmutable());

            // Assign role-specific permissions
            $roles = ['ROLE_USER'];
            if ($user->getRole() === UserRole::COMPANY) {
                $roles[] = 'ROLE_COMPANY';
            } elseif ($user->getRole() === UserRole::STUDENT) {
                $roles[] = 'ROLE_STUDENT';
            }
            $user->setRoles($roles);

            // Initialize matching profile entity
            if ($user->getRole() === UserRole::STUDENT) {
                $studentProfile = new StudentProfile();
                $studentProfile->setUser($user);
                $studentProfile->setIsPublic(true);
                $studentProfile->setIsPaused(false);
                $user->setStudentProfile($studentProfile);
                $entityManager->persist($studentProfile);
            } elseif ($user->getRole() === UserRole::COMPANY) {
                $companyProfile = new CompanyProfile();
                $companyProfile->setUser($user);
                $companyProfile->setCompanyName(trim(($user->getFirstName() ?? '') . ' ' . ($user->getLastName() ?? '')));
                $user->setCompanyProfile($companyProfile);
                $entityManager->persist($companyProfile);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Félicitations, votre compte a bien été créé ! Bienvenue sur Tindernance.');

            // Authenticate user directly
            $security->login($user, 'form_login', 'main');

            // Redirect directly to their dedicated space
            if ($user->getRole() === UserRole::COMPANY) {
                return $this->redirectToRoute('app_recruteur_profil');
            }

            return $this->redirectToRoute('app_profil_etudiant');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
