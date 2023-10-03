<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;
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
}
