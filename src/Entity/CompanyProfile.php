<?php

namespace App\Entity;

use App\Enum\EmployeeCountRange;
use App\Repository\CompanyProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyProfileRepository::class)]
class CompanyProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $companyName = null;

    #[ORM\Column(length: 255)]
    private ?string $logoUrl = null;

    #[ORM\Column]
    private array $mediaGallery = [];

    #[ORM\OneToOne(inversedBy: 'companyProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(enumType: EmployeeCountRange::class)]
    private ?EmployeeCountRange $employeeCount = null;

    #[ORM\Column(length: 255)]
    private ?string $sector = null;

    #[ORM\Column(length: 255)]
    private ?string $shortPresentation = null;

    /**
     * @var Collection<int, JobOffer>
     */
    #[ORM\OneToMany(targetEntity: JobOffer::class, mappedBy: 'companyprofile', orphanRemoval: true)]
    private Collection $jobOffers;

    public function __construct()
    {
        $this->jobOffers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(string $logoUrl): static
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getMediaGallery(): array
    {
        return $this->mediaGallery;
    }

    public function setMediaGallery(array $mediaGallery): static
    {
        $this->mediaGallery = $mediaGallery;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEmployeeCount(): ?EmployeeCountRange
    {
        return $this->employeeCount;
    }

    public function setEmployeeCount(EmployeeCountRange $employeeCount): static
    {
        $this->employeeCount = $employeeCount;

        return $this;
    }

    public function getSector(): ?string
    {
        return $this->sector;
    }

    public function setSector(string $sector): static
    {
        $this->sector = $sector;

        return $this;
    }

    public function getShortPresentation(): ?string
    {
        return $this->shortPresentation;
    }

    public function setShortPresentation(string $shortPresentation): static
    {
        $this->shortPresentation = $shortPresentation;

        return $this;
    }

    /**
     * @return Collection<int, JobOffer>
     */
    public function getJobOffers(): Collection
    {
        return $this->jobOffers;
    }

    public function addJobOffer(JobOffer $jobOffer): static
    {
        if (!$this->jobOffers->contains($jobOffer)) {
            $this->jobOffers->add($jobOffer);
            $jobOffer->setCompanyprofile($this);
        }

        return $this;
    }

    public function removeJobOffer(JobOffer $jobOffer): static
    {
        if ($this->jobOffers->removeElement($jobOffer)) {
            // set the owning side to null (unless already changed)
            if ($jobOffer->getCompanyprofile() === $this) {
                $jobOffer->setCompanyprofile(null);
            }
        }

        return $this;
    }
}
