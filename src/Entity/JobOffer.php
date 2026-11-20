<?php

namespace App\Entity;

use App\Enum\ContractDuration;
use App\Enum\OfferStatus;
use App\Enum\StudyLevel;
use App\Repository\JobOfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
class JobOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $missionDescription = null;

    #[ORM\Column(length: 255)]
    private ?string $websiteUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $tutorName = null;

    #[ORM\Column(length: 255)]
    private ?string $exactAdress = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: StudyLevel::class)]
    private array $requiredStudyLevel = [];

    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: ContractDuration::class)]
    private array $acceptedDurations = [];

    #[ORM\Column(length: 255)]
    private ?string $acceptedRhythm = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(nullable: true)]
    private ?array $perks = null;

    #[ORM\Column(enumType: OfferStatus::class)]
    private ?OfferStatus $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'jobOffers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CompanyProfile $companyprofile = null;

    /**
     * @var Collection<int, Swipe>
     */
    #[ORM\OneToMany(targetEntity: Swipe::class, mappedBy: 'joboffer')]
    private Collection $swipes;

    /**
     * @var Collection<int, MatchConversation>
     */
    #[ORM\OneToMany(targetEntity: MatchConversation::class, mappedBy: 'joboffer')]
    private Collection $matchConversations;

    public function __construct()
    {
        $this->swipes = new ArrayCollection();
        $this->matchConversations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMissionDescription(): ?string
    {
        return $this->missionDescription;
    }

    public function setMissionDescription(string $missionDescription): static
    {
        $this->missionDescription = $missionDescription;

        return $this;
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(string $websiteUrl): static
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    public function getTutorName(): ?string
    {
        return $this->tutorName;
    }

    public function setTutorName(string $tutorName): static
    {
        $this->tutorName = $tutorName;

        return $this;
    }

    public function getExactAdress(): ?string
    {
        return $this->exactAdress;
    }

    public function setExactAdress(string $exactAdress): static
    {
        $this->exactAdress = $exactAdress;

        return $this;
    }

    /**
     * @return StudyLevel[]
     */
    public function getRequiredStudyLevel(): array
    {
        return $this->requiredStudyLevel;
    }

    public function setRequiredStudyLevel(array $requiredStudyLevel): static
    {
        $this->requiredStudyLevel = $requiredStudyLevel;

        return $this;
    }

    /**
     * @return ContractDuration[]
     */
    public function getAcceptedDurations(): array
    {
        return $this->acceptedDurations;
    }

    public function setAcceptedDurations(array $acceptedDurations): static
    {
        $this->acceptedDurations = $acceptedDurations;

        return $this;
    }

    public function getAcceptedRhythm(): ?string
    {
        return $this->acceptedRhythm;
    }

    public function setAcceptedRhythm(string $acceptedRhythm): static
    {
        $this->acceptedRhythm = $acceptedRhythm;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getPerks(): ?array
    {
        return $this->perks;
    }

    public function setPerks(?array $perks): static
    {
        $this->perks = $perks;

        return $this;
    }

    public function getStatus(): ?OfferStatus
    {
        return $this->status;
    }

    public function setStatus(OfferStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCompanyprofile(): ?CompanyProfile
    {
        return $this->companyprofile;
    }

    public function setCompanyprofile(?CompanyProfile $companyprofile): static
    {
        $this->companyprofile = $companyprofile;

        return $this;
    }

    /**
     * @return Collection<int, Swipe>
     */
    public function getSwipes(): Collection
    {
        return $this->swipes;
    }

    public function addSwipe(Swipe $swipe): static
    {
        if (!$this->swipes->contains($swipe)) {
            $this->swipes->add($swipe);
            $swipe->setJoboffer($this);
        }

        return $this;
    }

    public function removeSwipe(Swipe $swipe): static
    {
        if ($this->swipes->removeElement($swipe)) {
            // set the owning side to null (unless already changed)
            if ($swipe->getJoboffer() === $this) {
                $swipe->setJoboffer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MatchConversation>
     */
    public function getMatchConversations(): Collection
    {
        return $this->matchConversations;
    }

    public function addMatchConversation(MatchConversation $matchConversation): static
    {
        if (!$this->matchConversations->contains($matchConversation)) {
            $this->matchConversations->add($matchConversation);
            $matchConversation->setJoboffer($this);
        }

        return $this;
    }

    public function removeMatchConversation(MatchConversation $matchConversation): static
    {
        if ($this->matchConversations->removeElement($matchConversation)) {
            // set the owning side to null (unless already changed)
            if ($matchConversation->getJoboffer() === $this) {
                $matchConversation->setJoboffer(null);
            }
        }

        return $this;
    }
}
