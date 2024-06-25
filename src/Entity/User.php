<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 15)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $state = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $Campus = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class,  mappedBy: 'user')]
    private Collection $Events;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(mappedBy: 'oganized', targetEntity: Event::class)]
    private Collection $eventsOrganized;

    public function __construct()    {
        $this->Events = new ArrayCollection();
        $this->eventsOrganized = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

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

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->Campus;
    }

    public function setCampus(?Campus $Campus): static
    {
        $this->Campus = $Campus;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->Events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->Events->contains($event)) {
            $this->Events->add($event);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        $this->Events->removeElement($event);

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEventsOrganized(): Collection
    {
        return $this->eventsOrganized;
    }

    public function addEventsOrganized(Event $eventsOrganized): static
    {
        if (!$this->eventsOrganized->contains($eventsOrganized)) {
            $this->eventsOrganized->add($eventsOrganized);
            $eventsOrganized->setOrganizer($this);
        }

        return $this;
    }

    public function removeEventsOrganized(Event $eventsOrganized): static
    {
        if ($this->eventsOrganized->removeElement($eventsOrganized)) {
            // set the owning side to null (unless already changed)
            if ($eventsOrganized->getOrganizer() === $this) {
                $eventsOrganized->setOrganizer(null);
            }
        }

        return $this;
    }
}
