<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CompanyProfileDto
{
    #[Assert\NotBlank(message: 'Le nom de l\'entreprise est requis.')]
    #[Assert\Length(min: 2, max: 255)]
    public string $companyName = '';

    #[Assert\NotBlank(message: 'Le secteur d\'activité est requis.')]
    #[Assert\Length(max: 255)]
    public string $sector = '';

    #[Assert\NotBlank(message: 'La présentation courte est requise.')]
    #[Assert\Length(min: 10, max: 500)]
    public string $shortPresentation = '';

    #[Assert\NotBlank(message: 'La taille de l\'entreprise est requise.')]
    public string $employeeCount = '11-49';

    public ?string $logoUrl = null;
}
