<?php

namespace App\Controller;

use App\Form\LoginFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="app_login_display", methods={"GET"})
     */
    public function displayLoginForm(AuthenticationUtils $authenticationUtils): Response
    {
    $form = $this->createForm(LoginFormType::class);

    $error = $authenticationUtils->getLastAuthenticationError();

    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', [
        'loginForm' => $form->createView(),
        'error' => $error,
        'last_mail' => $lastUsername,
    ]);
}

    /**
     * @Route("/login", name="app_login_submit", methods={"POST"})
     */
    public function login( Request $request, UserPasswordHasherInterface $passwordHasher, TokenStorageInterface $tokenStorage, UserRepository $userRepository): Response
    {
        // Crée un formulaire de connexion à l'aide du LoginFormType
        $form = $this->createForm(LoginFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupére les données du formulaire
            $data = $form->getData();

            
            $user = $userRepository->findOneByMail(['mail' => $data['mail']]);

            if (!$user) {
                // L'utilisateur n'existe pas, redirige avec un message d'erreur
                return $this->redirectToRoute('app_backoffice_home', ['error' => 'Invalid credentials']);
            }

            // Vérifiez le mot de passe
            if ($passwordHasher->isPasswordValid($user, $data['password'])) {
                // Authentification réussie, crée un jeton et authentifie l'utilisateur
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $tokenStorage->setToken($token);

                // Redirigez l'utilisateur vers la page de destination après la connexion
                return $this->redirectToRoute('product_list');
            } else {
                // Mot de passe incorrect, redirige avec un message d'erreur
                return $this->redirectToRoute('app_backoffice_home', ['error' => 'Invalid credentials']);
            }
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, redirige vers la page de connexion
        return $this->redirectToRoute('app_backoffice_home');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        // on ne devrait jamais arriver jusqu'ici
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Modification mot de passe User
     * 
     * @Route("/security/password", name="app_security_password", methods={"GET", "POST"})
     */
    public function password(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        // on crée un formulaire, non lié à une entité
        $form = $this->createForm(UserPasswordType::class);
        // le formulaire traite la requête
        $form->handleRequest($request);

        // si form valide et soumis
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère le nouveau mot de passe dans le formulaire
            $newPassword = $form->get('password')->getData();
            // on récupère l'utilisateur connecté
            // @see https://symfony.com/doc/5.4/security.html#fetching-the-user-object
            /** @var User $user L'utilisateur connecté */
            $user = $this->getUser();
            // on le hache
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            // on modifie son mot de passe
            $user->setPassword($hashedPassword);

            // on sauvegarde l'utilisateur
            $entityManager->flush($user);

            // on redirige
            return $this->redirectToRoute('home');
        }

        // on affiche le form
        return $this->renderForm('security/password.html.twig', [
            'form' => $form,
        ]);
    }
}