<?php

// src/Service/LegalAndPolicyService.php

namespace App\Service;

use App\Entity\LegalAndPolicy;
use App\Repository\LegalAndPolicyRepository;

class LegalAndPolicyService
{
    private LegalAndPolicyRepository $legalAndPolicyRepository;

    public function __construct(LegalAndPolicyRepository $legalAndPolicyRepository)
    {
        $this->legalAndPolicyRepository = $legalAndPolicyRepository;
    }

    public function getLegalAndPolicy(): ?LegalAndPolicy
    {
        return $this->legalAndPolicyRepository->findOne();
    }

    public function saveLegalAndPolicy(LegalAndPolicy $legalAndPolicy): void
    {
        $this->legalAndPolicyRepository->save($legalAndPolicy);
    }

    public function getLegalNotice(): ?string
    {
        $legalAndPolicy = $this->getLegalAndPolicy();

        return $legalAndPolicy ? $legalAndPolicy->getLegalNotice() : null;
    }

    public function getConfidentialPolicy(): ?string
    {
        $legalAndPolicy = $this->getLegalAndPolicy();

        return $legalAndPolicy ? $legalAndPolicy->getConfidentialPolicy() : null;
    }
}
