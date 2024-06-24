<?php

namespace App\Entity;

use App\Repository\StateEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StateEventRepository::class)]
class StateEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idStateEvent = null;

    #[ORM\Column(length: 255)]
    private ?string $wording = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdStateEvent(): ?int
    {
        return $this->idStateEvent;
    }

    public function setIdStateEvent(int $idStateEvent): static
    {
        $this->idStateEvent = $idStateEvent;

        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): static
    {
        $this->wording = $wording;

        return $this;
    }
}