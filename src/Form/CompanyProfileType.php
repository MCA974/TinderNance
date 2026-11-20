<?php

namespace App\Form;

use App\DTO\CompanyProfileDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CompanyProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'required' => true,
            ])
            ->add('contactName', TextType::class, [
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('sector', TextType::class, [
                'required' => true,
            ])
            ->add('employeeCount', HiddenType::class, [
                'required' => true,
            ])
            ->add('shortPresentation', TextareaType::class, [
                'required' => false,
            ])
            ->add('isPublic', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isPaused', HiddenType::class, [
                'required' => false,
            ])
            ->add('logoFile', FileType::class, [
                'required' => false,
                'mapped' => true,
                'label' => false,
                'constraints' => [
                    new File(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'],
                        mimeTypesMessage: 'Veuillez uploader une image valide (JPG, PNG, WEBP, SVG).',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyProfileDto::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'company_profile_item',
        ]);
    }
}
