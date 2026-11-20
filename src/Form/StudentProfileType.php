<?php

namespace App\Form;

use App\DTO\StudentProfileDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('searchRegion', TextType::class, [
                'required' => false,
            ])
            ->add('searchRadius', IntegerType::class, [
                'required' => false,
            ])
            ->add('schoolName', TextType::class, [
                'required' => false,
            ])
            ->add('obtainedDiploma', TextType::class, [
                'required' => false,
            ])
            ->add('schoolCampus', TextType::class, [
                'required' => false,
            ])
            ->add('alternationRhythm', HiddenType::class, [
                'required' => false,
            ])
            ->add('contractDuration', HiddenType::class, [
                'required' => false,
            ])
            ->add('desiredStartDate', TextType::class, [
                'required' => false,
            ])
            ->add('bio', TextareaType::class, [
                'required' => false,
            ])
            ->add('isPublic', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isPaused', HiddenType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StudentProfileDto::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'student_profile_item',
        ]);
    }
}
