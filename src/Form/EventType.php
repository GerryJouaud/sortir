<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\StateEvent;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name' ,TextType::class,[
                'label' => 'Nom de la sortie :',
            ])

            ->add('startDate', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie : ',
            ])

            ->add('dateLine', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => "Date limite d'inscription : ",
            ])


            ->add('maxParticipants' , TextType::class,[
                'label' => 'Nombre de participants :',
            ])

            ->add('description' ,TextareaType::class,[
                'label' => 'Description de la sortie : ',
            ])

            ->add('duration',TextType::class, [
                'label' => 'Durée (en minutes): '
            ])

            ->add('stateEvent', EntityType::class, [
                'class' => StateEvent::class,
                'choice_label' => 'wording',
            ])

            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'name',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
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
