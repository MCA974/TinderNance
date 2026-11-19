---
name: symfony-doctrine-orm
description: >-
  Skills et bonnes pratiques pour Doctrine ORM 3.x dans Symfony 8.1.
  Couvre les entités, repositories, migrations, requêtes DQL/QueryBuilder et règles de sécurité de base de données.
---

# Compétence Symfony 8.1 : Doctrine ORM & Persistance

Documentation officielle : [https://symfony.com/doc/8.1/doctrine.html](https://symfony.com/doc/8.1/doctrine.html)

---

## ⛔ RÈGLE CRITIQUE ET ABSOLUE : FICHIERS D'ENTITÉS (`src/Entity/`)

> [!CAUTION]
> **PROTECTION DES FICHIERS ENTITY**
>
> 1. **INTERDICTION FORMELLE DE MODIFIER DIRECTEMENT LES FICHIERS ENTITY** situés dans `src/Entity/` (ex: `Arbre.php`, `Voiture.php`, etc.).
> 2. **AUTORISATION EXPLICITE REQUISE** : Demande **TOUJOURS** l'autorisation de l'utilisateur avant d'éditer ou altérer une entité.
> 3. **CONDITION STRICTE** : Tu ne dois demander cette autorisation **SEULEMENT SI le changement dans le projet est CONSÉQUENT** (refonte majeure, nouvelle fonctionnalité clé impactant la modélisation des données). Si le changement n'est pas conséquent, ne modifie pas les entités et ne propose pas de les altérer.

---

## 1. Principes clés Doctrine dans Symfony 8.1

- **Attributs PHP 8** : Déclarer le mapping uniquement avec les attributs natifs (`#[ORM\Entity]`, `#[ORM\Table]`, `#[ORM\Id]`, `#[ORM\GeneratedValue]`, `#[ORM\Column]`, `#[ORM\ManyToOne]`, etc.).
- **Constructeurs et types PHP** : Utiliser le typage strict PHP 8.4+, les types nullables (`?string`), et l'initialisation des collections dans le constructeur (`new ArrayCollection()`).
- **Repositories personnalisés** : Étendre `ServiceEntityRepository` et utiliser l'injection de dépendances (`ManagerRegistry $registry`).
- **Migrations obligatoires** :
  - Ne **JAMAIS** utiliser `doctrine:schema:update`.
  - Toujours générer une migration : `php bin/console make:migration`.
  - Appliquer les migrations : `php bin/console doctrine:migrations:migrate --no-interaction`.

## 2. Exemple d'utilisation type

### Repository personnalisé
```php
namespace App\Repository;

use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voiture>
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    /**
     * @return Voiture[]
     */
    public function findRecent(int $limit = 10): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
```

### Persistence dans un Service
```php
use Doctrine\ORM\EntityManagerInterface;

class VoitureManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function save(Voiture $voiture): void
    {
        $this->entityManager->persist($voiture);
        $this->entityManager->flush();
    }
}
```

## 3. Checklist de vérification
- [ ] Les fichiers sous `src/Entity/` n'ont pas été modifiés sans accord préalable.
- [ ] Si une modification a été approuvée, une migration a été générée et testée.
- [ ] Les requêtes utilisent des paramètres préparés (`setParameter`) pour éviter toute injection SQL.
