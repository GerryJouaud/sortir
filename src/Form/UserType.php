<?php
// src/Form/UserType.php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName')
            ->add('firstName')
            ->add('phone')
            ->add('email')
            // Ajoute un champ pour le campus, en utilisant l'EntityType pour choisir parmi les entités Campus
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'id',
            ])
            // Ajoute un champ pour le mot de passe avec une confirmation
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs de mot de passe doivent correspondre.',
                'options' => ['attr' => ['autocomplete' => 'new-password']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            // Ajoute un champ pour le fichier du poster
            ->add('poster', FileType::class, [
                'mapped' => false,
                'required' => false, // Le champ n'est pas obligatoire
                'constraints' => [
                    new Image(
                        [
                            'maxSize' => '10000k',
                            'mimeTypesMessage' => 'Format d\'image non autorisé !',
                            'maxSizeMessage' => 'Fichier trop volumineux !',
                        ]
                    )
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
