<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column]
    private ?bool $is_public = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'events')]
    private Collection $participant;

    #[ORM\ManyToOne(inversedBy: 'listEventCreated')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $creator = null;
    private $remainingPlaces;
    public function __construct()
    {
        $this->participant = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->is_public;
    }

    public function setIsPublic(bool $is_public): static
    {
        $this->is_public = $is_public;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }
    public function getRemainingPlaces(): ?int
    {
        return $this->remainingPlaces;
    }

    public function setRemainingPlaces(int $remainingPlaces): self
    {
        $this->remainingPlaces = $remainingPlaces;

        return $this;
    }

    public function addListUser(User $listUser): static
    {
        if (!$this->participant->contains($listUser)) {
            $this->participant->add($listUser);
            $listUser->addParticpantInEvent($this);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        $this->participant->removeElement($participant);
        $participant->removeEvent($this);

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }
}
