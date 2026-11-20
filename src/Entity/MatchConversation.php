<?php

namespace App\Entity;

use App\Enum\MatchStatus;
use App\Repository\MatchConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchConversationRepository::class)]
class MatchConversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: MatchStatus::class)]
    private ?MatchStatus $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $matchedAt = null;

    #[ORM\OneToOne(inversedBy: 'matchConversation', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Swipe $swipe = null;

    #[ORM\ManyToOne(inversedBy: 'matchConversations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StudentProfile $studentprofile = null;

    #[ORM\ManyToOne(inversedBy: 'matchConversations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobOffer $joboffer = null;

    /**
     * @var Collection<int, Message>
     */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'matchconversation', orphanRemoval: true)]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?MatchStatus
    {
        return $this->status;
    }

    public function setStatus(MatchStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getMatchedAt(): ?\DateTimeImmutable
    {
        return $this->matchedAt;
    }

    public function setMatchedAt(\DateTimeImmutable $matchedAt): static
    {
        $this->matchedAt = $matchedAt;

        return $this;
    }

    public function getSwipe(): ?Swipe
    {
        return $this->swipe;
    }

    public function setSwipe(Swipe $swipe): static
    {
        $this->swipe = $swipe;

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

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setMatchconversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getMatchconversation() === $this) {
                $message->setMatchconversation(null);
            }
        }

        return $this;
    }
}
