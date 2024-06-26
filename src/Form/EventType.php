<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\StateEvent;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie : '
            ])

            ->add('startDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie : '
            ])

            ->add('duration', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie : '
            ])
            ->add('dateLine', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Durée (en minutes):'
            ])

            ->add('maxParticipants')

            ->add('description',TextareaType::class, [
              'label' => 'Description et informations de la sortie : '
               ])

            ->add('stateEvent', EntityType::class, [
                'class' => StateEvent::class,
                'choice_label' => 'wording',
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',
                'multiple' => true,
            ])
            ->add('organizer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstName',
            ])

            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
                'label' => 'Lieu : '
            ])
            ->add('campus', EntityType::class, [
                'choice_label' => 'name',
                'label' => 'Campus : ',
                'class' => Campus::class,
                'placeholder' => 'Choisir un campus'

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
