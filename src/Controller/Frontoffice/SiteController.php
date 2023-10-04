<?php

namespace App\Controller\Frontoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;
use App\Repository\CategorieRepository;
use App\Entity\Categorie;
use App\Entity\Marque;

class SiteController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        $categorie = $entityManager->getRepository(Categorie::class)->findAll();

        $marque = $entityManager->getRepository(Marque::class)->findAll();

        // Spécifie l'ordre pour récupérer les produits triés
        $order = 'ASC';
        // Récupére les trois produits avec l'ordre défini depuis la base de données
        $products = $productRepository->findByOrder($order);

        return $this->render('frontoffice/accueil.html.twig', [
            'controller_name' => 'MainController',
            'products' => $products,
            'categorie' => $categorie,
            'marque' => $marque,
        ]);
    }

    /**
     * @Route("/categories/{categoryId}/products", name="products_by_category")
     */
    public function listProductsByCategories(int $categoryId, CategorieRepository $categorieRepository): Response
    {

        $category = $categorieRepository->find($categoryId);


        if (!$category) {
            throw $this->createNotFoundException('La catégorie spécifiée n\'existe pas.');
        }

        // Vous devez également récupérer les produits liés à cette catégorie ici
        $products = $category->getProducts();

        return $this->render('frontoffice/categorie/list_products_by_categories.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
