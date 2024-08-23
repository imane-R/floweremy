<?php
// src/Controller/LegalAndPolicyController.php

namespace App\Controller;

use App\Service\LegalAndPolicyService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/legal-policy')]
class LegalAndPolicyController extends AbstractController
{
    private LegalAndPolicyService $legalAndPolicyService;

    public function __construct(LegalAndPolicyService $legalAndPolicyService)
    {
        $this->legalAndPolicyService = $legalAndPolicyService;
    }

    #[Route('/legalNotice ', name: 'legal_notice', methods: ['GET'])]
    public function privacy(): Response
    {
        $legalNotice = $this->legalAndPolicyService->getLegalNotice();

        return $this->render('legal_and_policy/legalNotice.html.twig', [
            'legalNotice' => $legalNotice
        ]);
    }

    #[Route('/confidentialPolicy', name: 'confidentialPolicy', methods: ['GET'])]
    public function terms(): Response
    {
        $confidentialPolicy = $this->legalAndPolicyService->getConfidentialPolicy();

        return $this->render('legal_and_policy/confidentialPolicy.html.twig', [
            'confidentialPolicy' => $confidentialPolicy
        ]);
    }

    #[Route('/ConditionsOfSale', name: 'ConditionsOfSale', methods: ['GET'])]
    public function sale(): Response
    {
        $ConditionsOfSale = $this->legalAndPolicyService->getConditionsOfSale();

        return $this->render('legal_and_policy/ConditionsOfSale.html.twig', [
            'ConditionsOfSale' => $ConditionsOfSale
        ]);
    }
}
