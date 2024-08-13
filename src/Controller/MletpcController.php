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
    #[Route('/admin_mletpc_add', name: 'mletpc_add')]
    public function addmlandpc(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mletpc = new Mletpc;
        $form = $this->createForm(MletpcType::class, $mletpc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($mletpc);
            $entityManager->flush();
            return $this->redirectToRoute('mletpc_add');
        }
        return $this->render("mletpc/form.html.twig", [
            'formMletpc' => $form->createView(),
        ]);
    }
    #[Route('/admin_mletpc_update', name: 'mletpc_update')]
    public function mletpcupdate(Request $request, MletpcRepository $repo)
    {
        $mletpc = $repo->find(1);
        $form = $this->createForm(MletpcType::class,  $mletpc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repo->save($mletpc, 1);
            return $this->redirectToRoute('mletpc_update');
        }

        return $this->render('mletpc/form.html.twig', [
            'formMletpc' => $form->createView(),

        ]);
    }
}
