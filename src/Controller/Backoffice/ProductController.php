<?php
namespace App\Controller\Backoffice;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use App\Entity\Categorie;
use App\Entity\Marque;
use App\Form\ProductType;
use App\Form\EditProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\EventListener\UpdateOrderSubscriber;
use App\Service\PlayerBgColorService;
use DateTime;

/**
 * @Route("/backoffice")
 * 
 */
class ProductController extends AbstractController
{
    const PRODUCT = "product";

    /**
     * Afficher la liste des produits
     *
     * @Route("/product", name="product_list", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('backoffice/main/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    public function processForm(Request $request, EntityManagerInterface $entityManager, Product $product, UpdateOrderSubscriber $updateOrderSubscriber): Response
    {
        // Récupérez toutes les catégories depuis la base de données
        $categories = $entityManager->getRepository(Categorie::class)->findAll();

        // Récupérez toutes les marques depuis la base de données
        $marques = $entityManager->getRepository(Marque::class)->findAll();

        // Récupérez tout les statuts depuis la base de données
        $statuts = $entityManager->getRepository(Marque::class)->findAll();

        // Passez les marques au formulaire
        $form = $this->createForm(ProductType::class, $product, [
            'marques' => $marques,
            'categories' => $categories,
            'statuts' => $statuts,
            'update_order_subscriber' => $updateOrderSubscriber,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($product);
            $entityManager->flush();

            // Rediriger vers la liste des produits après création
            return $this->redirectToRoute('product_list');
        }

        $view = $product->getId() ? 'backoffice/main/' . self::PRODUCT . '/edit.html.twig' : 'backoffice/main/' . self::PRODUCT . '/new.html.twig';

        return $this->render($view, [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * Créer un nouveau produit
     *
     * @Route("/product/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, UpdateOrderSubscriber $updateOrderSubscriber): Response
    {
        $product = new Product();
        return $this->processForm($request, $entityManager, $product, $updateOrderSubscriber);
    }

    /**
     * Modifier un produit existant
     *
     * @Route("/product/edit/{id}", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, Int $id, UpdateOrderSubscriber $updateOrderSubscriber): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        return $this->processForm($request, $entityManager, $product, $updateOrderSubscriber);
    }

    /**
     * Supprimer un produit
     *
     * @Route("/product/delete/{id}", name="product_delete",  methods={"POST"})
     */
    public function delete( EntityManagerInterface $entityManager, Product $product): Response
    {
        // Vérifiez que le produit existe
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }
        
            $entityManager->remove($product);
            $entityManager->flush();

        // Rediriger vers la liste des produits après suppression
        return $this->redirectToRoute('product_list');
    }

    /**
     * Page d'affichage des informations d'un produit spécifique.
     * 
     * @Route("/product/show/{id}", name="product_show", methods={"GET"})
     */
    public function show($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if ($product === null) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        return $this->render('backoffice/main/' . self::PRODUCT . '/show.html.twig', [
            self::PRODUCT => $product,
        ]);
    }

}
