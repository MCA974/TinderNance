---
name: symfony-controller-routing
description: >-
  Skills pour la gestion des Contrôleurs et du Routage dans Symfony 8.1.
  Utiliser pour créer ou modifier des contrôleurs, gérer les requêtes HTTP, le binding de payload (MapRequestPayload, MapQueryString) et les réponses.
---

# Compétence Symfony 8.1 : Contrôleurs et Routage

Documentation officielle :
- [https://symfony.com/doc/8.1/controller.html](https://symfony.com/doc/8.1/controller.html)
- [https://symfony.com/doc/8.1/routing.html](https://symfony.com/doc/8.1/routing.html)

---

## 1. Principes essentiels Symfony 8.1

- **Contrôleurs minces (*Thin Controllers*)** : Le contrôleur doit déléguer les calculs et la logique métier à des services dédiés.
- **Attributs de routage** : Toujours utiliser `#[Route]` sur les méthodes d'action ou sur la classe.
- **Binding de données automatique** :
  - `#[MapRequestPayload]` : convertit et valide automatiquement le corps JSON d'une requête en DTO avec le composant Serializer et Validator.
  - `#[MapQueryString]` : convertit et valide les query parameters de l'URL.
  - `#[MapDateTime]` : convertit les paramètres date en objets `\DateTimeImmutable`.
- **Génération d'URL** : Utiliser `$this->generateUrl()` en PHP ou `path()` / `url()` dans Twig, jamais de chaînes codées en dur.

## 2. Exemple d'action de contrôleur moderne (API / Web)

```php
namespace App\Controller;

use App\Dto\CreateItemDto;
use App\Service\ItemService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/items')]
class ItemController extends AbstractController
{
    public function __construct(
        private readonly ItemService $itemService,
    ) {}

    #[Route('', name: 'app_item_list', methods: ['GET'])]
    public function index(): Response
    {
        $items = $this->itemService->getAllItems();

        return $this->render('item/index.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/api', name: 'app_item_api_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createApi(
        #[MapRequestPayload] CreateItemDto $dto,
    ): JsonResponse {
        $item = $this->itemService->createFromDto($dto);

        return $this->json($item, Response::HTTP_CREATED);
    }
}
```

## 3. Commandes de débogage
```bash
php bin/console debug:router
php bin/console router:match /items
```
