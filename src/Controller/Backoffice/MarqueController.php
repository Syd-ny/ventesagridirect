<?php
namespace App\Controller\Backoffice;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Marque;
use App\Form\MarqueType;
use App\Repository\MarqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PlayerBgColorService;
use DateTime;

/**
 * @Route("/backoffice")
 * 
 */
class MarqueController extends AbstractController
{
    const MARQUE = "marque";

    /**
     * Afficher la liste des marques
     *
     * @Route("/marque", name="marque_list", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $marques = $entityManager->getRepository(Marque::class)->findAll();

        return $this->render('backoffice/main/' . self::MARQUE . '/index.html.twig', [
            self::MARQUE . 's' => $marques,
        ]);
    }

    /**
     * Processus commun pour la création et la modification de marque
     */
    private function processForm(Request $request, EntityManagerInterface $entityManager, Marque $marque): Response
    {
        // Mettez ici la logique commune au formulaire de création et de modification
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($marque);
            $entityManager->flush();

            // Rediriger vers la liste des marques après création ou modification
            return $this->redirectToRoute('marque_list');
        }

        $view = $marque->getId() ? 'backoffice/main/' . self::MARQUE . '/edit.html.twig' : 'backoffice/main/' . self::MARQUE . '/new.html.twig';

        return $this->render($view, [
            'form' => $form->createView(),
            'marque' => $marque,
        ]);
    }

    /**
     * Créer une nouvelle marque
     *
     * @Route("/marque/new", name="marque_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $marque = new Marque();
        return $this->processForm($request, $entityManager, $marque);
    }

    /**
     * Modifier une marque existante
     *
     * @Route("/marque/edit/{id}", name="marque_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, Int $id): Response
    {
        $marque = $entityManager->getRepository(Marque::class)->find($id);
        if (!$marque) {
            throw $this->createNotFoundException('Marque non trouvée');
        }
        return $this->processForm($request, $entityManager, $marque);
    }

    /**
     * Supprimer une marque
     *
     * @Route("/marque/delete/{id}", name="marque_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, Marque $marque): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marque->getId(), $request->request->get('_token'))) {
            $entityManager->remove($marque);
            $entityManager->flush();
        }

        // Rediriger vers la liste des marques après suppression
        return $this->redirectToRoute('marque_list');
    }

    /**
     * Page d'affichage des informations d'une marque spécifique.
     * 
     * @Route("/marque/show/{id}", name="marque_show", methods={"GET"})
     */
    public function show($id, MarqueRepository $marqueRepository): Response
    {
        $marque = $marqueRepository->find($id);

        if ($marque === null) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        return $this->render('backoffice/main/' . self::MARQUE . '/show.html.twig', [
            self::MARQUE => $marque,
        ]);
    }
}
