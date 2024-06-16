<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;


    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide.")]
    #[Assert\Email(message: "Veuillez entrer un email valide.")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private ?array $roles = [];

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'participant')]
    private Collection $events;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'creator')]
    private Collection $listEventCreated;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->listEventCreated = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addParticpantInEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
        }
        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            $event->removeParticipant($this);
        }

        return $this;
    }

    public function getRoles(): array
    {
        // Retourne les rôles définis pour l'utilisateur
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si vous avez des données sensibles temporaires, nettoyez-les ici
    }

    public function getUserIdentifier(): string
    {
        return $this->email; // Utilisez l'email comme identifiant unique de l'utilisateur
    }

    /**
     * @return Collection<int, Event>
     */
    public function getListEventCreated(): Collection
    {
        return $this->listEventCreated;
    }

    public function addListEventCreated(Event $listEventCreated): static
    {
        if (!$this->listEventCreated->contains($listEventCreated)) {
            $this->listEventCreated->add($listEventCreated);
            $listEventCreated->setCreator($this);
        }

        return $this;
    }

    public function removeEventCreated(Event $listEventCreated): static
    {
        if ($this->listEventCreated->removeElement($listEventCreated)) {
            // set the owning side to null (unless already changed)
            if ($listEventCreated->getCreator() === $this) {
                $listEventCreated->setCreator(null);
            }
        }

        return $this;
    }
}
