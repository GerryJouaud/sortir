<?php

namespace App\Utils;

use App\Repository\EventRepository;
use App\Repository\StateEventRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class Update{

    private EventRepository $eventRepository;
    private StateEventRepository $stateEventRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EventRepository $eventRepository,
                                StateEventRepository $stateEventRepository,
                                EntityManagerInterface $entityManager
    ){
        $this->eventRepository = $eventRepository;
        $this->stateEventRepository = $stateEventRepository;
        $this->entityManager = $entityManager;
    }


    //fonction qui récupère tous les états de la BDD
    public function tableState(){

        $states = [];
        foreach ( $this->stateEventRepository->findAll() as $state){
              $i = $state->getWording();
              $states[$i] = $state;

        }
        return $states;
    }


    public function updateState($states):void{

        //modifier statut d'un évènement suivant la date de début de la sortie
        //récupère toutes les sorties avec le repository

        $events = $this->eventRepository->findAllEventsUpdate();
        foreach ($events as $event){
            //récupère le statut de l'évènement
            $status = $event->getState()->getWording();

            //variable stocke la fin de l'évènement (date + durée)
            $event1 = clone $event->getStartDateTime();
            $event1->modify("+" .$event->getDuration() . "month");

            //condition pour modifier l'évènement
            //cas où ça date de plus de 1 mois
            if($event1 < new \DateTime('-1 month')){
                $state = $states['archived'];
                $event->setStateEvent($state);

                //entre 1 mois et aujourd'hui
            } else  if ($event1 < new DateTime('-1 month'))  {
                $state = $states['finished'];
                $event->setStateEvent($state);

            } else if ($event->getStartDateTime() < new DateTime()) {
                $state = $states['inProgress'];

                $event->setStateEvent($state);
            } else if ((new DateTime() > $event->getRegistrationDeadline())){
                $state = $states['closed'];
                $event->setStateEvent($state);

            }
            //permet de faire le persist et le flush -> modifie la base de données
            $this->entityManager->persist($event);
        }
        $this->entityManager->flush($events);

            }

}