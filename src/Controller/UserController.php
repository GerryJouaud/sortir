<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\CityRepository;
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


    #[Route('/update/{id}', name: 'update')]
    public function update(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        int $id
    ): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException("Cet utilisateur n'a pas été trouvé");
        }
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil utilisateur mis à jour avec succès.');

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            "userForm"=>$userForm
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
    #[\Symfony\Component\Routing\Attribute\Route('/details{id}', name: 'details', requirements: ['id' => '\d+'])]
    public function details(UserRepository $userRepository, int $id): Response{
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException("Cet utilisateur n'a pas été trouvée");
        }
        return $this->render('user/userDetails.html.twig', [
            "user" => $user,
        ]);
    }

}


