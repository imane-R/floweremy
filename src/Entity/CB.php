<?php

namespace App\Entity;

use App\Repository\CBRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CBRepository::class)]
class CB
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $numero_carte = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_exp = null;

    #[ORM\Column(length: 4)]
    private ?string $cvv = null;

    #[ORM\ManyToOne(inversedBy: 'cbs')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCarte(): ?string
    {
        return $this->numero_carte;
    }

    public function setNumeroCarte(string $numero_carte): static
    {
        $this->numero_carte = $numero_carte;

        return $this;
    }

    public function getDateExp(): ?\DateTimeInterface
    {
        return $this->date_exp;
    }

    public function setDateExp(\DateTimeInterface $date_exp): static
    {
        $this->date_exp = $date_exp;

        return $this;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function setCvv(string $cvv): static
    {
        $this->cvv = $cvv;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
