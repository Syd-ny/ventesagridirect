<?php
namespace App\Controller\Backoffice;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Form\EditCategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PlayerBgColorService;
use DateTime;

/**
 * @Route("/backoffice")
 * 
 */
class CategorieController extends AbstractController
{

    const CATEGORIE = "categorie";

    /**
     * Afficher la liste des categories
     *
     * @Route("/categorie", name="categorie_list", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Categorie::class)->findAll();

        return $this->render('backoffice/main/' . self::CATEGORIE . '/index.html.twig', [
            self::CATEGORIE . 's' => $categories,
        ]);
    }

    /**
     * Processus commun pour la création et la modification de catégorie
     */
    private function processForm(Request $request, EntityManagerInterface $entityManager, Categorie $categorie): Response
    {
        // Mettez ici la logique commune au formulaire de création et de modification
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();

            // Rediriger vers la liste des catégories après création ou modification
            return $this->redirectToRoute('categorie_list');
        }

        $view = $categorie->getId() ? 'backoffice/main/' . self::CATEGORIE . '/edit.html.twig' : 'backoffice/main/' . self::CATEGORIE . '/new.html.twig';

        return $this->render($view, [
            'form' => $form->createView(),
            'categorie' => $categorie,
        ]);
    }

    /**
     * Créer une nouvelle catégorie
     *
     * @Route("/categorie/new", name="categorie_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        return $this->processForm($request, $entityManager, $categorie);
    }

    /**
     * Modifier une catégorie existante
     *
     * @Route("/categorie/edit/{id}", name="categorie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, Int $id): Response
    {
        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
        return $this->processForm($request, $entityManager, $categorie);
    }

    /**
     * Supprimer une categorie
     *
     * @Route("/categorie/delete/{id}", name="categorie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        // Rediriger vers la liste des categories après suppression
        return $this->redirectToRoute('categorie_list');
    }

    /**
     * Page d'affichage des informations d'une categorie spécifique.
     * 
     * @Route("/categorie/show/{id}", name="categorie_show", methods={"GET"})
     */
    public function show($id, CategorieRepository $categorieRepository): Response
    {
        $categorie = $categorieRepository->find($id);

        if ($categorie === null) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        return $this->render('backoffice/main/' . self::CATEGORIE . '/show.html.twig', [
            self::CATEGORIE => $categorie,
        ]);
    }
}
