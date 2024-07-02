<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Utils\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/user', name: 'user_')]
#[IsGranted('ROLE_USER')] // Accessible seulement aux utilisateurs authentifiés
class UserController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/userList.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/create', name: 'create')]
    #[IsGranted('ROLE_USER')] // Accessible seulement aux utilisateurs authentifiés
    public function create(
        Request                $request,
        EntityManagerInterface $entityManager,
        FileUploader           $fileUploader
    ): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var UploadedFile $file
             */
            // Récupère le fichier téléchargé
            $file = $form->get('poster')->getData();
            $newFilename = $fileUploader->upload($file,
                $this->getParameter('sortir_poster_directory'),
                $user->getFirstName());
            // Définit le nouveau nom du fichier dans l'objet User
            $user->setPoster($newFilename);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');

            // Redirige vers la liste des utilisateurs
            return $this->redirectToRoute('user_list', [
                'user' => $user,
                'form' => $form,
            ]);
        }
// Retourne la vue de création d'utilisateur avec le formulaire
        return $this->render('', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'Recherche')]
    #[IsGranted('ROLE_USER')] // Accessible seulement aux utilisateurs authentifiés
    public function show(UserRepository $userRepository, int $id): Response
    {
        // Récupère l'utilisateur par son ID
        $user = $userRepository->find($id);

        // Si l'utilisateur n'est pas trouvé, lève une exception
        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        return $this->render('user/Recherche.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(Request                $request,
                           EntityManagerInterface $entityManager,
                           UserRepository         $userRepository,
                           FileUploader           $fileUploader,
                           int                    $id): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException("Cet utilisateur n'a pas été trouvé");
        }

        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            // Vérifier si un fichier a été téléchargé pour le champ 'poster'
            $file = $userForm->get('poster')->getData();
            if ($file) {
                // Télécharge le fichier et récupère le nouveau nom de fichier
                $newFilename = $fileUploader->upload(
                    $file,
                    $this->getParameter('sortir_poster_directory'),
                    $user->getFirstName()
                );
                // Mettez à jour le nom du poster dans l'entité User
                $user->setPoster($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil utilisateur mis à jour avec succès.');

            return $this->redirectToRoute('user_details', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'userForm' => $userForm,
            'user' => $user,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
       // Restriction d'accès aux utilisateurs ayant le rôle ROLE_ADMIN
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Supprime l'utilisateur de la base de données
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('user_list');
    }

    // Route pour afficher les détails d'un utilisateur avec des exigences sur l'ID (doit être un nombre)
    #[Route('/details/{id}', name: 'details', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')] // Accessible seulement aux utilisateurs authentifiés
    public function details(
        UserRepository $userRepository,
        int            $id
    ): Response
    {
        // Récupère l'utilisateur par son ID
        $user = $userRepository->find($id);
        // Vérifie si l'utilisateur connecté peut accéder à cet utilisateur
        if (!$this->isGranted('ROLE_ADMIN') && $user !== $this->getUser()) {
            throw new AccessDeniedException('Vous n\'avez pas le droit d\'accéder à cet utilisateur.');
        }

        // Si l'utilisateur n'est pas trouvé, lève une exception
        if (!$user) {
            throw $this->createNotFoundException("Cet utilisateur n'a pas été trouvé");
        }

        return $this->render('user/userDetails.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/registrationsList/{id}', name: 'registrationsList', requirements: ['id' => '\d+']), ]
    #[IsGranted('ROLE_USER')] // Accessible seulement aux utilisateurs authentifiés
    public function registrationsList(UserRepository $userRepository, int $id): Response
    {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException("Cet utilisateur n'a pas été trouvé");
        }
        $userRegistrationsList = $user->getEvents();
        if (!$userRegistrationsList) {
            throw $this->createNotFoundException("La liste de sorties n'a pas été trouvée !");
        }
        if ($userRegistrationsList->isEmpty()) {
            throw $this->createNotFoundException("Aucune inscription !");
        }

        return $this->render('user/userRegistrationsList.html.twig', [
            "user" => $user,
            "userRegistrationsList" => $userRegistrationsList
        ]);
    }
}
