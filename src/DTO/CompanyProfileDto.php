<?php

namespace App\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyProfileDto
{
    #[Assert\NotBlank(message: 'Le nom de l\'entreprise est obligatoire.')]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $companyName = null;

    #[Assert\NotBlank(message: 'Le nom du contact ou recruteur est obligatoire.')]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $contactName = null;

    #[Assert\NotBlank(message: 'L\'adresse email est obligatoire.')]
    #[Assert\Email(message: 'Format d\'email invalide.')]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Le secteur d\'activité est requis.')]
    #[Assert\Length(max: 100)]
    public ?string $sector = null;

    #[Assert\NotBlank(message: 'La taille de l\'entreprise est requise.')]
    public ?string $employeeCount = '11-49';

    #[Assert\Length(max: 500, maxMessage: 'La présentation ne doit pas dépasser {{ limit }} caractères.')]
    public ?string $shortPresentation = null;

    public ?string $logoUrl = null;

    /** @var UploadedFile|null */
    public mixed $logoFile = null;

    public bool $isPublic = true;

    public bool $isPaused = false;
}
