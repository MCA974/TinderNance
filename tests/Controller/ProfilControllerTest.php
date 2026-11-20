<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfilControllerTest extends WebTestCase
{
    public function testProfilEtudiantPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/profil/etudiant');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.brand-logo');
        $this->assertSelectorTextContains('.brand-logo', 'Tindernance');
        $this->assertSelectorExists('.pe-navbar');
    }
}
