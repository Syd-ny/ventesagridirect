<?php
namespace App\Controller\Backoffice;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\PlayerBgColorService;
use DateTime;

/**
 * @Route("/backoffice")
 * 
 */
class UserController extends AbstractController
{

    const USER = "user";

    /**
     * Afficher la liste des users
     *
     * @Route("/user", name="user_list", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('backoffice/main/' . self::USER . '/index.html.twig', [
            self::USER . 's' => $users,
        ]);
    }

    /**
     * Processus commun pour la création et la modification d'utilisateur
     */
    private function processForm(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        // Mettez ici la logique commune au formulaire de création et de modification
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            // Rediriger vers la liste des utilisateurs après création ou modification
            return $this->redirectToRoute('user_list');
        }

        $view = $user->getId() ? 'backoffice/main/' . self::USER . '/edit.html.twig' : 'backoffice/main/' . self::USER . '/new.html.twig';

        return $this->render($view, [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Créer un nouveau utilisateur
     *
     * @Route("/user/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        return $this->processForm($request, $entityManager, $user);
    }

    /**
     * Modifier un utilisateur existant
     *
     * @Route("/user/edit/{id}", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, Int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        return $this->processForm($request, $entityManager, $user);
    }

    /**
     * Supprimer un utilisateur
     *
     * @Route("/user/delete/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        // Rediriger vers la liste des users après suppression
        return $this->redirectToRoute('user_list');
    }

    /**
     * Page d'affichage des informations d'une user spécifique.
     * 
     * @Route("/user/show/{id}", name="user_show", methods={"GET"})
     */
    public function show($id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if ($user === null) {
            throw $this->createNotFoundException('Catégorie non trouvée.');
        }

        return $this->render('backoffice/main/' . self::USER . '/show.html.twig', [
            self::USER => $user,
        ]);
    }
}
