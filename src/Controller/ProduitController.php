<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitFormType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProduitController extends AbstractController
{
    #[Route('/admin_produits_add', name: 'add_produits')]
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
    #[Route('/produit/{id}', name: 'app_produit_show')]
    public function show(Produit $produit, ProduitRepository $produitRepository): Response
    {
        // Fetch d'autre produits, excluding the current one
        $produits = $produitRepository->findBy([], null, 4); // fetch 4 produits
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'produits' => $produits
        ]);
    }
    #[Route('/search', name: 'product_search')]
    public function search(Request $request, ProduitRepository $produitRepository): Response
    {
        $query = $request->query->get('q');

        $produits = $produitRepository->createQueryBuilder('p')
            ->where('p.nom_produit LIKE :query')
            ->orWhere('p.couleur LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        return $this->render('produit/search_results.html.twig', [
            'produits' => $produits,
            'query' => $query
        ]);
    }

    #[Route('/admin_produits', name: 'produits')]
    public function showAll(ProduitRepository $repo)
    {
        $produits = $repo->findAll();
        return $this->render("produit/showAllProduits.html.twig", [
            'produits' => $produits
        ]);
    }

    #[Route('/admin_produits_edit/{id}', name: 'edit_produit')]
    public function editProduit(Produit $produits, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitFormType::class, $produits);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('produits');
        }
        return $this->render("produit/form.html.twig", [
            'formProduits' => $form->createView()
        ]);
    }

    #[Route('/admin_produits_delete/{id}', name: 'delete_produit')]
    public function deleteProduit(Produit $produits, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($produits);
        $entityManager->flush();
        return $this->redirectToRoute('produits');
    }
}
