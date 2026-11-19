---
name: symfony-service-container
description: >-
  Skills pour le Conteneur de Services et l'Injection de Dépendances dans Symfony 8.1.
  Utiliser pour l'autowiring, l'autoconfiguration, les attributs #[Autowire], #[Target], #[AsCommand], et les services immuables.
---

# Compétence Symfony 8.1 : Conteneur de Services & Injection de Dépendances

Documentation officielle :
[https://symfony.com/doc/8.1/service_container.html](https://symfony.com/doc/8.1/service_container.html)

---

## 1. Principes et Bonnes Pratiques

- **Autowiring & Autoconfiguration** : Les services sont automatiquement résolus par typage d'interface ou de classe dans le constructeur.
- **Promotion des propriétés de constructeur** : Simplifie l'injection de dépendances en PHP 8.x.
- **Attributs Symfony de conteneur** :
  - `#[Autowire(param: 'kernel.project_dir')]` : Injecter un paramètre de configuration.
  - `#[Autowire(env: 'APP_SECRET')]` : Injecter une variable d'environnement.
  - `#[Autowire(service: 'service_id')]` : Injecter un service spécifique sans fichier YAML.
  - `#[Target('primaryStorage')]` : Choisir une implémentation parmi plusieurs services implémentant la même interface.
  - `#[AsCommand(name: 'app:do-task')]` : Enregistrer automatiquement une commande console.
  - `#[AsEventListener]` et `#[AsMessageHandler]` : Enregistrer automatiquement un écouteur ou handler.

## 2. Exemple de service avec autowiring avancé

```php
namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class FileUploader
{
    public function __construct(
        #[Autowire(param: 'kernel.project_dir')]
        private readonly string $projectDir,

        #[Autowire(env: 'STORAGE_BASE_PATH')]
        private readonly string $storageBasePath,

        private readonly LoggerInterface $logger,
    ) {}

    public function getTargetDirectory(): string
    {
        return $this->projectDir . '/' . trim($this->storageBasePath, '/');
    }
}
```

## 3. Commandes utiles
```bash
php bin/console debug:container
php bin/console debug:autowiring <nom_ou_interface>
php bin/console lint:container
```
