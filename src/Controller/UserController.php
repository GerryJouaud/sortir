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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/user',name: 'user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(
        UserRepository $userRepository
    ): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/userList.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
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

            return $this->redirectToRoute('');//a change
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}', name: 'user_show', methods: ['GET'])]
    public function show(UserRepository $userRepository, int $id): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        User                   $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Profil utilisateur mis à jour avec succès.');

            return $this->redirectToRoute('');//a changer
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

//delete
    #[Route('/{id}', name: 'user_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request                $request,
        User                   $user,
        EntityManagerInterface $entityManager
    ): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('');//a changer
    }

}


