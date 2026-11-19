---
name: symfony-forms-validation
description: >-
  Skills pour les Formulaires et la Validation dans Symfony 8.1.
  Utiliser pour concevoir des FormTypes, valider les entrées utilisateurs avec les attributs #[Assert\...], gérer le CSRF et les DTOs.
---

# Compétence Symfony 8.1 : Formulaires & Validation

Documentation officielle :
- [https://symfony.com/doc/8.1/forms.html](https://symfony.com/doc/8.1/forms.html)
- [https://symfony.com/doc/8.1/validation.html](https://symfony.com/doc/8.1/validation.html)

---

## 1. Principes et Bonnes Pratiques

- **FormTypes autonomes** : Définir des classes étendant `AbstractType` plutôt que de construire les formulaires à la volée dans le contrôleur.
- **DTOs pour les formulaires complexes** : Découpler le formulaire des entités Doctrine si le formulaire implique des transformations ou plusieurs modèles (utiliser des DTOs typés).
- **Attributs de validation natifs** : Utiliser les attributs `#[Assert\...]` sur les propriétés de classe.
- **Protection CSRF** : Active par défaut avec Symfony Forms.
- **Gestion des erreurs** : Affichage standardisé avec Twig (`form_errors()`, `form_row()`).

## 2. Exemple de DTO validé & FormType

### DTO avec attributs de validation
```php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Le nom est obligatoire')]
        #[Assert\Length(min: 2, max: 100)]
        public ?string $nom = null,

        #[Assert\NotBlank(message: 'L\'email est obligatoire')]
        #[Assert\Email(message: 'Email invalide')]
        public ?string $email = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10)]
        public ?string $message = null,
    ) {}
}
```

### FormType
```php
namespace App\Form;

use App\Dto\ContactDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDto::class,
        ]);
    }
}
```

### Traitement dans le Contrôleur
```php
#[Route('/contact', name: 'app_contact')]
public function contact(Request $request): Response
{
    $dto = new ContactDto();
    $form = $this->createForm(ContactType::class, $dto);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Traiter les données validées
        $this->addFlash('success', 'Votre message a été envoyé avec succès !');
        return $this->redirectToRoute('app_contact');
    }

    return $this->render('contact/index.html.twig', [
        'form' => $form,
    ]);
}
```
