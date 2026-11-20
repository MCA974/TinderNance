<?php

namespace App\Entity;

use App\Enum\SwipeAction;
use App\Repository\SwipeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SwipeRepository::class)]
class Swipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: SwipeAction::class)]
    private ?SwipeAction $studentAction = null;

    #[ORM\Column(enumType: SwipeAction::class)]
    private ?SwipeAction $companyAction = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $studentSwipedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $companySwipedAt = null;

    #[ORM\Column]
    private ?bool $isRewound = null;

    #[ORM\ManyToOne(inversedBy: 'swipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StudentProfile $studentprofile = null;

    #[ORM\ManyToOne(inversedBy: 'swipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobOffer $joboffer = null;

    #[ORM\OneToOne(mappedBy: 'swipe', cascade: ['persist', 'remove'])]
    private ?MatchConversation $matchConversation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudentAction(): ?SwipeAction
    {
        return $this->studentAction;
    }

    public function setStudentAction(SwipeAction $studentAction): static
    {
        $this->studentAction = $studentAction;

        return $this;
    }

    public function getCompanyAction(): ?SwipeAction
    {
        return $this->companyAction;
    }

    public function setCompanyAction(SwipeAction $companyAction): static
    {
        $this->companyAction = $companyAction;

        return $this;
    }

    public function getStudentSwipedAt(): ?\DateTimeImmutable
    {
        return $this->studentSwipedAt;
    }

    public function setStudentSwipedAt(\DateTimeImmutable $studentSwipedAt): static
    {
        $this->studentSwipedAt = $studentSwipedAt;

        return $this;
    }

    public function getCompanySwipedAt(): ?\DateTimeImmutable
    {
        return $this->companySwipedAt;
    }

    public function setCompanySwipedAt(\DateTimeImmutable $companySwipedAt): static
    {
        $this->companySwipedAt = $companySwipedAt;

        return $this;
    }

    public function isRewound(): ?bool
    {
        return $this->isRewound;
    }

    public function setIsRewound(bool $isRewound): static
    {
        $this->isRewound = $isRewound;

        return $this;
    }

    public function getStudentprofile(): ?StudentProfile
    {
        return $this->studentprofile;
    }

    public function setStudentprofile(?StudentProfile $studentprofile): static
    {
        $this->studentprofile = $studentprofile;

        return $this;
    }

    public function getJoboffer(): ?JobOffer
    {
        return $this->joboffer;
    }

    public function setJoboffer(?JobOffer $joboffer): static
    {
        $this->joboffer = $joboffer;

        return $this;
    }

    public function getMatchConversation(): ?MatchConversation
    {
        return $this->matchConversation;
    }

    public function setMatchConversation(MatchConversation $matchConversation): static
    {
        // set the owning side of the relation if necessary
        if ($matchConversation->getSwipe() !== $this) {
            $matchConversation->setSwipe($this);
        }

        $this->matchConversation = $matchConversation;

        return $this;
    }
}
