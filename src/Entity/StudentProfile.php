<?php

namespace App\Entity;

use App\Enum\ContractDuration;
use App\Enum\SchoolStatus;
use App\Enum\StudyLevel;
use App\Repository\StudentProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentProfileRepository::class)]
class StudentProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoUrl = null;

    #[ORM\Column(enumType: StudyLevel::class, nullable: true)]
    private ?StudyLevel $targetStudyLevel = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $obtainedDiploma = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $schoolName = null;

    #[ORM\Column(enumType: SchoolStatus::class, nullable: true)]
    private ?SchoolStatus $schoolStatus = null;

    #[ORM\Column(enumType: ContractDuration::class, nullable: true)]
    private ?ContractDuration $contractDuration = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $alternationRhythm = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $desiredStartDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cvUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $targetSector = null;

    #[ORM\Column]
    private ?bool $isPublic = false;

    #[ORM\Column]
    private ?bool $isPaused = false;

    #[ORM\OneToOne(inversedBy: 'studentProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Embedded(class: SearchArea::class)]
    private SearchArea $searchArea;

    /**
     * @var Collection<int, Swipe>
     */
    #[ORM\OneToMany(targetEntity: Swipe::class, mappedBy: 'studentprofile')]
    private Collection $swipes;

    /**
     * @var Collection<int, MatchConversation>
     */
    #[ORM\OneToMany(targetEntity: MatchConversation::class, mappedBy: 'studentprofile')]
    private Collection $matchConversations;

    public function __construct()
    {
        $this->swipes = new ArrayCollection();
        $this->matchConversations = new ArrayCollection();
        $this->searchArea = new SearchArea();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(string $photoUrl): static
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getTargetStudyLevel(): ?StudyLevel
    {
        return $this->targetStudyLevel;
    }

    public function setTargetStudyLevel(StudyLevel $targetStudyLevel): static
    {
        $this->targetStudyLevel = $targetStudyLevel;

        return $this;
    }

    public function getObtainedDiploma(): ?string
    {
        return $this->obtainedDiploma;
    }

    public function setObtainedDiploma(string $obtainedDiploma): static
    {
        $this->obtainedDiploma = $obtainedDiploma;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getSchoolName(): ?string
    {
        return $this->schoolName;
    }

    public function setSchoolName(string $schoolName): static
    {
        $this->schoolName = $schoolName;

        return $this;
    }

    public function getSchoolStatus(): ?SchoolStatus
    {
        return $this->schoolStatus;
    }

    public function setSchoolStatus(SchoolStatus $schoolStatus): static
    {
        $this->schoolStatus = $schoolStatus;

        return $this;
    }

    public function getContractDuration(): ?ContractDuration
    {
        return $this->contractDuration;
    }

    public function setContractDuration(ContractDuration $contractDuration): static
    {
        $this->contractDuration = $contractDuration;

        return $this;
    }

    public function getAlternationRhythm(): ?string
    {
        return $this->alternationRhythm;
    }

    public function setAlternationRhythm(string $alternationRhythm): static
    {
        $this->alternationRhythm = $alternationRhythm;

        return $this;
    }

    public function getDesiredStartDate(): ?\DateTimeImmutable
    {
        return $this->desiredStartDate;
    }

    public function setDesiredStartDate(\DateTimeImmutable $desiredStartDate): static
    {
        $this->desiredStartDate = $desiredStartDate;

        return $this;
    }

    public function getCvUrl(): ?string
    {
        return $this->cvUrl;
    }

    public function setCvUrl(string $cvUrl): static
    {
        $this->cvUrl = $cvUrl;

        return $this;
    }

    public function getTargetSector(): ?string
    {
        return $this->targetSector;
    }

    public function setTargetSector(string $targetSector): static
    {
        $this->targetSector = $targetSector;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function isPaused(): ?bool
    {
        return $this->isPaused;
    }

    public function setIsPaused(bool $isPaused): static
    {
        $this->isPaused = $isPaused;

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

    /**
     * @return Collection<int, Swipe>
     */
    public function getSwipes(): Collection
    {
        return $this->swipes;
    }

    public function getSearchArea(): SearchArea
    {
        return $this->searchArea;
    }

    public function setSearchArea(SearchArea $searchArea): static
    {
        $this->searchArea = $searchArea;

        return $this;
    }

    public function addSwipe(Swipe $swipe): static
    {
        if (!$this->swipes->contains($swipe)) {
            $this->swipes->add($swipe);
            $swipe->setStudentprofile($this);
        }

        return $this;
    }

    public function removeSwipe(Swipe $swipe): static
    {
        if ($this->swipes->removeElement($swipe)) {
            // set the owning side to null (unless already changed)
            if ($swipe->getStudentprofile() === $this) {
                $swipe->setStudentprofile(null);
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
            $matchConversation->setStudentprofile($this);
        }

        return $this;
    }

    public function removeMatchConversation(MatchConversation $matchConversation): static
    {
        if ($this->matchConversations->removeElement($matchConversation)) {
            // set the owning side to null (unless already changed)
            if ($matchConversation->getStudentprofile() === $this) {
                $matchConversation->setStudentprofile(null);
            }
        }

        return $this;
    }
}
