<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\StateEvent;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startDate', null, [
                'widget' => 'single_text',
            ])
            ->add('duration', null, [
                'widget' => 'single_text',
            ])
            ->add('dateLine', null, [
                'widget' => 'single_text',
            ])
            ->add('maxParticipants')
            ->add('description')
            ->add('stateEvent', EntityType::class, [
                'class' => StateEvent::class,
                'choice_label' => 'id',
            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('organizer', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'id',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'id',
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
