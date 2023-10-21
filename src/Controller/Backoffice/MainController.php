<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\LoginFormType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


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
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // en cas de POST + erreur
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $this->getUser();

        // Créez le formulaire de connexion en utilisant le formulaire que vous avez généré
        $form = $this->createForm(LoginFormType::class);

        // Récupérez les erreurs d'authentification, le cas échéant
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'user' => $user,
            'loginForm' => $form->createView(),
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

}