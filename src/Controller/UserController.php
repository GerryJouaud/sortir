<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(
        UserRepository $userRepository
    ): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Profil utilisateur mis à jour avec succès.');

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('user_index');
    }


    #[Route('/login', name: 'user_login', methods: ['GET', 'POST'])]
    public function login(
        AuthenticationUtils $authenticationUtils
    ): Response
    {
        // Récupérer l'erreur de login s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupérer le dernier username saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'user_logout', methods: ['GET'])]
    public function logout(): void
    {
      //todo
    }
}

