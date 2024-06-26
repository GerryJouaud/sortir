<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

    class SecurityController extends AbstractController
    {
        #[Route(path:'/login', name: 'user_login')]
        public function login(AuthenticationUtils $authenticationUtils): Response
        {
            if ($this->getUser()) {
                return $this->redirectToRoute('state_event_index');
            }

            // Récupérer l'erreur de login s'il y en a une
            $error = $authenticationUtils->getLastAuthenticationError();

            // Récupérer le dernier username saisi par l'utilisateur
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }

        #[Route(path:'/logout', name: 'user_logout')]
        public function logout(): void
        {

        }
    }