<?php

namespace App\Controller;

use App\Entity\Mletpc;
use App\Form\MletpcType;
use App\Repository\MletpcRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MletpcController extends AbstractController
{
    #[Route('/mentionslegales', name: 'app_mentionslegales')]
    public function mentionslegales(MletpcRepository $repo)
    {
        $mletpc = $repo->findAll();
        return $this->render('mletpc/index.html.twig', [
            'mletpcs' =>  $mletpc
        ]);
    }
}
