---
name: symfony-testing-quality
description: >-
  Skills pour les tests automatisés (PHPUnit, WebTestCase, KernelTestCase) et la qualité de code dans Symfony 8.1.
  Utiliser pour rédiger des tests fonctionnels HTTP, des tests unitaires/d'intégration et exécuter les linters de validation.
---

# Compétence Symfony 8.1 : Tests Automatisés & Qualité de Code

Documentation officielle :
[https://symfony.com/doc/8.1/testing.html](https://symfony.com/doc/8.1/testing.html)

---

## 1. Principes et Conventions

- **Types de tests** :
  - **Tests fonctionnels / HTTP** : Étendre `WebTestCase`, utiliser `static::createClient()` pour simuler des requêtes HTTP complètes.
  - **Tests d'intégration de services** : Étendre `KernelTestCase`, récupérer les services via `static::getContainer()->get(...)`.
  - **Tests unitaires purs** : Étendre `TestCase` de PHPUnit.
- **Linters obligatoires avant livraison** :
  - `php bin/console lint:container`
  - `php bin/console lint:twig templates/`
  - `php bin/console lint:yaml config/`

## 2. Exemple de Test Fonctionnel (WebTestCase)

```php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VoitureControllerTest extends WebTestCase
{
    public function testIndexPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/voitures');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }

    public function testCreateActionRedirectsOnSuccess(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/voitures/new');

        $buttonCrawlerModifier = $crawler->selectButton('Enregistrer');
        $form = $buttonCrawlerModifier->form([
            'voiture[marque]' => 'Peugeot',
            'voiture[modele]' => '208',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/voitures');
    }
}
```

## 3. Commandes d'exécution des tests
```bash
# Lancer toute la suite de tests
php bin/phpunit

# Lancer un fichier spécifique
php bin/phpunit tests/Controller/VoitureControllerTest.php
```
