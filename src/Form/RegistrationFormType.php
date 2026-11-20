<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role', EnumType::class, [
                'class' => UserRole::class,
                'choices' => [UserRole::STUDENT, UserRole::COMPANY],
                'choice_label' => fn (UserRole $role) => match ($role) {
                    UserRole::STUDENT => 'Étudiant(e)',
                    UserRole::COMPANY => 'Entreprise',
                    default => $role->value,
                },
                'expanded' => true,
                'multiple' => false,
                'label' => 'Je suis',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Ex : Alex',
                    'autocomplete' => 'given-name',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre prénom'),
                    new Length(max: 50, maxMessage: 'Le prénom ne doit pas dépasser {{ limit }} caractères'),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Ex : Dupont',
                    'autocomplete' => 'family-name',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner votre nom'),
                    new Length(max: 50, maxMessage: 'Le nom ne doit pas dépasser {{ limit }} caractères'),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'placeholder' => 'alex.dupont@exemple.fr',
                    'autocomplete' => 'email',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner une adresse email'),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => '••••••••',
                    'autocomplete' => 'new-password',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez renseigner un mot de passe'),
                    new Length(
                        min: 6,
                        minMessage: 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        max: 4096,
                    ),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte les conditions d\'utilisation et la politique de confidentialité de Tindernance',
                'constraints' => [
                    new IsTrue(message: 'Vous devez accepter les conditions pour vous inscrire.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
