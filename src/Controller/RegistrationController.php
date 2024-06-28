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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);

            /**
             * @var UploadedFile $file
             */
            //récupération du fichier de type UploadedFile
            $file = $form->get('poster')->getData();
            $newFilename = $fileUploader->upload(
                $file,
                $this->getParameter('sortir_poster_directory'),
                $user->getFirstName());
            //setté le nouveau nom dans l'objet
            $user->setPoster($newFilename);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Registration successful!');

            return $userAuthenticator->authenticateUser(
                $user,
                $eventAuthenticator,
                $request
            );

            // Optionally, redirect to a specific route after registration
            // return $this->redirectToRoute('app_main'); // Replace 'app_main' with your main route
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
