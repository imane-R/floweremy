<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProduitController extends AbstractController
{
    #[Route('/produits_add', name: 'add_produits')]
    public function addProduit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $produits = new Produit;
        $form = $this->createForm(ProduitFormType::class, $produits);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageForm')->getData();
            if ($file) {
                $fileName = $produits->getNomProduit() . uniqid() . '.' . $file->guessExtension();
                try {
                    //pn cherche à enregistrer l'image du formulaire dans notre dossier paramétré dans service.yaml "service_images" sous le nom q'on a crée "$titre"
                    $file->move($this->getParameter('produits_images'), $fileName);
                } catch (FileException $e) {
                    //gérer les exceptions en cas d'erreur durant l'upload d'un article
                }
                $produits->setImage($fileName);
            }

            //ajouter un vaaluer dans slug de la database
            $entityManager->persist($produits);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }
        $data = ['url' => '/add_produits'];
        return $this->render("produit/form.html.twig", [
            'formProduits' => $form->createView(),
            'data' => $data
        ]);
    }
}
