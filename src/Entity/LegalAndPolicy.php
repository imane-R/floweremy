<?php
// src/Entity/LegalAndPolicyPage.php

namespace App\Entity;

use App\Repository\LegalAndPolicyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LegalAndPolicyRepository::class)]
class LegalAndPolicy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $legalNotice = null;

    #[ORM\Column(type: 'text')]
    private ?string $confidentialPolicy = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ConditionsOfSale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLegalNotice(): ?string
    {
        return $this->legalNotice;
    }

    public function setLegalNotice(string $legalNotice): self
    {
        $this->legalNotice = $legalNotice;
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function getConfidentialPolicy(): ?string
    {
        return $this->confidentialPolicy;
    }

    public function setConfidentialPolicy(string $confidentialPolicy): self
    {
        $this->confidentialPolicy = $confidentialPolicy;
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getConditionsOfSale(): ?string
    {
        return $this->ConditionsOfSale;
    }

    public function setConditionsOfSale(?string $ConditionsOfSale): static
    {
        $this->ConditionsOfSale = $ConditionsOfSale;

        return $this;
    }
}
