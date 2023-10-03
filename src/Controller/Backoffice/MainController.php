<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LoginFormType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/**
 * @Route("/backoffice/home")
 * 
 */
class MainController extends AbstractController
{

    /**
     * Endpoint for displaying the backoffice homepage
     * 
     * @Route("/", name="app_backoffice_home")
     */
    public function home(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();

        // Créez le formulaire de connexion en utilisant le formulaire que vous avez généré
        $form = $this->createForm(LoginFormType::class);

        // Récupérez les erreurs d'authentification, le cas échéant
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('backoffice/main/base.html.twig', [
            'user' => $user,
            'loginForm' => $form->createView(),
            'error' => $error,
        ]);
    }

}