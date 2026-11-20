<?php

namespace App\Tests\Controller;

use App\Entity\CompanyProfile;
use App\Entity\StudentProfile;
use App\Entity\User;
use App\Enum\ContractDuration;
use App\Enum\EmployeeCountRange;
use App\Enum\SchoolStatus;
use App\Enum\StudyLevel;
use App\Enum\UserRole;
use App\Repository\JobOfferRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DecouverteControllerTest extends WebTestCase
{
    private function getOrCreateStudentUser(): User
    {
        $container = static::getContainer();
        $userRepository = $container->get(UserRepository::class);
        $studentUser = $userRepository->findOneBy(['role' => UserRole::STUDENT]);

        if ($studentUser && $studentUser->getStudentProfile()) {
            return $studentUser;
        }

        $em = $container->get(EntityManagerInterface::class);

        // Créer l'entreprise test si elle n'existe pas
        $companyProfile = $container->get(\App\Repository\CompanyProfileRepository::class)->findOneBy([]);
        if (!$companyProfile) {
            $companyUser = new User();
            $companyUser->setEmail('recruteur.test@payfit.com');
            $companyUser->setFirstName('PayFit');
            $companyUser->setLastName('Recrutement');
            $companyUser->setPassword('testpassword');
            $companyUser->setRole(UserRole::COMPANY);
            $companyUser->setRoles(['ROLE_USER', 'ROLE_COMPANY']);
            $companyUser->setCreatedAt(new \DateTimeImmutable());

            $companyProfile = new CompanyProfile();
            $companyProfile->setUser($companyUser);
            $companyProfile->setCompanyName('PayFit France');
            $companyProfile->setLogoUrl('/images/offers/banner_default.png');
            $companyProfile->setSector('Fintech & SaaS RH');
            $companyProfile->setShortPresentation('Scale-up RH');
            $companyProfile->setEmployeeCount(EmployeeCountRange::RANGE_250_PLUS);
            $companyUser->setCompanyProfile($companyProfile);

            $em->persist($companyUser);
            $em->persist($companyProfile);
        }

        // Créer l'étudiant test
        $studentUser = new User();
        $studentUser->setEmail('etudiant.test@tindernance.fr');
        $studentUser->setFirstName('Lucas');
        $studentUser->setLastName('Moreau');
        $studentUser->setPassword('testpassword');
        $studentUser->setRole(UserRole::STUDENT);
        $studentUser->setRoles(['ROLE_USER', 'ROLE_STUDENT']);
        $studentUser->setCreatedAt(new \DateTimeImmutable());

        $studentProfile = new StudentProfile();
        $studentProfile->setUser($studentUser);
        $studentProfile->setIsPublic(true);
        $studentProfile->setIsPaused(false);
        $studentProfile->setTargetStudyLevel(StudyLevel::BAC_PLUS_5);
        $studentProfile->setSchoolStatus(SchoolStatus::ENROLLED);
        $studentProfile->setContractDuration(ContractDuration::TWO_YEARS);
        $studentProfile->setAlternationRhythm('3j Entreprise / 2j École');
        $studentProfile->setDesiredStartDate(new \DateTimeImmutable('+3 months'));

        $searchArea = $studentProfile->getSearchArea();
        $searchArea->setCity('Paris');
        $searchArea->setDepartment('75');
        $searchArea->setRegion('Île-de-France');
        $searchArea->setRadiusKm(25);

        $studentUser->setStudentProfile($studentProfile);

        $em->persist($studentProfile);
        $em->persist($studentUser);
        $em->flush();

        return $studentUser;
    }

    public function testDecouverteRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/decouverte');

        $this->assertResponseRedirects('/login');
    }

    public function testDecouvertePageIsSuccessfulForStudent(): void
    {
        $client = static::createClient();
        $studentUser = $this->getOrCreateStudentUser();

        $client->loginUser($studentUser);
        $crawler = $client->request('GET', '/decouverte');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.brand-logo');
        $this->assertSelectorTextContains('.brand-logo', 'Tindernance');
        $this->assertSelectorExists('.dec-visibility-card');
        $this->assertSelectorExists('.dec-filters-card');
        $this->assertSelectorExists('.dec-controls-bar');
    }

    public function testSwipeAndRewindEndpoints(): void
    {
        $client = static::createClient();
        $studentUser = $this->getOrCreateStudentUser();

        $client->loginUser($studentUser);

        // Déclencher le chargement des offres
        $client->request('GET', '/decouverte');
        $this->assertResponseIsSuccessful();

        $jobOfferRepository = static::getContainer()->get(JobOfferRepository::class);
        $offers = $jobOfferRepository->findAll();
        $this->assertNotEmpty($offers, 'Des offres doivent être disponibles.');

        $firstOffer = $offers[0];

        // 1. Tester le Swipe LIKE
        $client->request(
            'POST',
            '/decouverte/swipe/' . $firstOffer->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['action' => 'LIKE'])
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);

        // 2. Tester le Rewind
        $client->request('POST', '/decouverte/rewind');
        $this->assertResponseIsSuccessful();
        $rewindData = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($rewindData['success']);
    }
}
