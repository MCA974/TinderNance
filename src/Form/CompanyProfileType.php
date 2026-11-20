<?php

namespace App\Form;

use App\DTO\CompanyProfileDto;
use App\Enum\EmployeeCountRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'attr' => [
                    'placeholder' => 'ex: Acme Corp',
                    'data-recruteur-target' => 'companyName',
                    'data-action' => 'input->recruteur#previewName',
                ],
            ])
            ->add('sector', TextType::class, [
                'label' => 'Secteur d\'activité',
                'attr' => [
                    'placeholder' => 'ex: Fintech, E-commerce, SaaS…',
                    'data-recruteur-target' => 'sector',
                ],
            ])
            ->add('shortPresentation', TextareaType::class, [
                'label' => 'Présentation courte',
                'attr' => [
                    'placeholder' => 'Décrivez votre entreprise en 2-3 phrases…',
                    'rows' => 3,
                    'data-recruteur-target' => 'bio',
                    'data-action' => 'input->recruteur#updateBio',
                ],
            ])
            ->add('employeeCount', ChoiceType::class, [
                'label' => 'Taille de l\'entreprise',
                'choices' => [
                    '1 – 10 collaborateurs' => EmployeeCountRange::RANGE_1_10->value,
                    '11 – 49 collaborateurs' => EmployeeCountRange::RANGE_11_49->value,
                    '50 – 249 collaborateurs' => EmployeeCountRange::RANGE_50_249->value,
                    '250+ collaborateurs' => EmployeeCountRange::RANGE_250_PLUS->value,
                ],
                'attr' => [
                    'data-recruteur-target' => 'employeeCount',
                    'data-action' => 'change->recruteur#updateCount',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyProfileDto::class,
        ]);
    }
}
