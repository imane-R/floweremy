<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieFormType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/admin_categorie_add', name: 'add_categorie')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie;
        $form = $this->createForm(CategorieFormType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();
            return $this->redirectToRoute('app_home');
        }

        $data = ['url' => '/add_categorie'];
        return $this->render("categorie/form.html.twig", [
            'formCategorie' => $form->createView(),
            'data' => $data
        ]);
    }

    #[Route('/categorie/{id}', name: 'category_show')]
    public function show(Categorie $categorie): Response
    {
        // Supposons que l'entité Categorie ait une relation OneToMany avec les produits
        $produits = $categorie->getProduits();

        return $this->render('produit/produit-categoeir.html.twig', [
            'categorie' => $categorie,
            'produits' => $produits,
        ]);
    }
    // Méthode dropdown pour le menu des catégories
    public function dropdown(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findAll();

        return $this->render('partials/category_menu.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin_categories', name: 'categories')]
    public function showAll(CategorieRepository $repo)
    {
        $categories = $repo->findAll();
        return $this->render("categorie/showAllCategories.html.twig", [
            'categories' => $categories

        ]);
    }

    #[Route('/admin_categorie_edit/{id}', name: 'edit_categorie')]
    public function edit(Categorie $categorie, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieFormType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('categories');
        }
        return $this->render("categorie/form.html.twig", [
            'formCategorie' => $form->createView()
        ]);
    }

    #[Route('/admin_categorie_delete/{id}', name: 'delete_categorie')]
    public function delete(Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($categorie);
        $entityManager->flush();
        return $this->redirectToRoute('categories');
    }
}
