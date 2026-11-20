<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class StudentProfileDto
{
    #[Assert\NotBlank(message: 'Le nom complet est obligatoire.')]
    #[Assert\Length(min: 2, max: 100)]
    public ?string $fullName = null;

    #[Assert\NotBlank(message: 'L\'adresse email est obligatoire.')]
    #[Assert\Email(message: 'Format d\'email invalide.')]
    public ?string $email = null;

    #[Assert\Length(max: 255)]
    public ?string $searchRegion = null;

    #[Assert\PositiveOrZero]
    public ?int $searchRadius = 25;

    #[Assert\Length(max: 100)]
    public ?string $schoolName = null;

    #[Assert\Length(max: 100)]
    public ?string $obtainedDiploma = null;

    #[Assert\Length(max: 100)]
    public ?string $schoolCampus = null;

    public ?string $schoolStatus = null;

    #[Assert\Length(max: 100)]
    public ?string $alternationRhythm = null;

    public ?string $contractDuration = null;

    public ?string $desiredStartDate = null;

    #[Assert\Length(max: 290, maxMessage: 'Le pitch ne doit pas dépasser {{ limit }} caractères.')]
    public ?string $bio = null;

    public ?string $photoUrl = null;

    public ?string $cvUrl = null;

    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null */
    public mixed $photoFile = null;

    /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null */
    public mixed $cvFile = null;

    public bool $isPublic = false;

    public bool $isPaused = false;
}
