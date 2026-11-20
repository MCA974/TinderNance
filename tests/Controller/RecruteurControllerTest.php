<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecruteurControllerTest extends WebTestCase
{
    public function testRecruteurProfilPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/recruteur/profil');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.brand-logo');
        $this->assertSelectorTextContains('.brand-logo', 'Tindernance');
        $this->assertSelectorExists('.rp-navbar');
    }
}
