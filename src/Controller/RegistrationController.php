<?php

namespace App\Controller;

use App\Utils\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\FormRegistryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\EventAuthenticator;

#[Route('/registration', name: 'app_registration')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        EventAuthenticator $eventAuthenticator,
        EntityManagerInterface $entityManager,
        FileUploader           $fileUploader
    ): Response
    {
        $user = new User();
        $form = $this->createForm(FormRegistryType::class, $user);
        // Traitement de la requête HTTP pour manipuler les données du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe avant de l'enregistrer dans la base de données
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // Attribution du rôle 'ROLE_USER' au nouvel utilisateur
            $user->setRoles(['ROLE_USER']);

            /**
             * @var UploadedFile $file
             */
            // Récupération du fichier téléchargé de type UploadedFile
            $file = $form->get('poster')->getData();
            // Utilisation du service FileUploader pour gérer l'upload du fichier
            $newFilename = $fileUploader->upload(
                $file,
                $this->getParameter('sortir_poster_directory'),
                $user->getFirstName());
            //setté le nouveau nom dans l'objet
            $user->setPoster($newFilename);
            // Persistance de l'entité User dans la base de données
            $entityManager->persist($user);
            // Enregistrement des changements dans la base de données
            $entityManager->flush();

            $this->addFlash('success', 'Enregistrement réussi!');
            // Authentification automatique de l'utilisateur après l'inscription
            return $this->redirectToRoute("user_details", ['id' => $user->getId()]);

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'user' =>$user
        ]);
    }
}
