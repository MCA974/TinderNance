---
name: symfony-security
description: >-
  Skills pour la Sécurité, l'Authentification et l'Autorisation dans Symfony 8.1.
  Utiliser pour configurer les pare-feux (firewalls), les authentificateurs, les électeurs (Voters), et le hachage des mots de passe.
---

# Compétence Symfony 8.1 : Sécurité, Authentification & Autorisation

Documentation officielle :
[https://symfony.com/doc/8.1/security.html](https://symfony.com/doc/8.1/security.html)

---

## 1. Principes et Architecture de Sécurité

- **Firewalls & Access Control** : Configurés dans `config/packages/security.yaml`.
- **Hachage sécurisé** : Utiliser `UserPasswordHasherInterface` (utilise par défaut bcrypt/argon2id).
- **Contrôle d'accès déclaratif** :
  - Attribut `#[IsGranted('ROLE_...')]` sur les contrôleurs ou méthodes d'actions.
  - `$this->denyAccessUnlessGranted('...')` dans les contrôleurs.
- **Voters sur mesure** : Pour les autorisations basées sur des règles métier complexes (ex: "est-ce que l'utilisateur est le propriétaire de cette ressource ?").

## 2. Exemple d'un Voter personnalisé

```php
namespace App\Security\Voter;

use App\Entity\Voiture;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class VoitureVoter extends Voter
{
    public const EDIT = 'VOITURE_EDIT';
    public const VIEW = 'VOITURE_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW], true)
            && $subject instanceof Voiture;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Voiture $voiture */
        $voiture = $subject;

        return match ($attribute) {
            self::VIEW => true,
            self::EDIT => $this->canEdit($voiture, $user),
            default => false,
        };
    }

    private function canEdit(Voiture $voiture, UserInterface $user): bool
    {
        // Règle métier : seul le propriétaire ou un administrateur peut modifier
        return in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
```

## 3. Commandes de vérification
```bash
php bin/console debug:firewall
php bin/console debug:autowiring security
```
