<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\PlaceRepository;
use App\Repository\StateEventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;




#[Route('/event', name: 'event_')]
class EventController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        PlaceRepository $placeRepository,
        StateEventRepository $stateEventRepository,

    ): Response
    {

     $events = $eventRepository->findAll();
//     $places = $placeRepository->findAll();

        //Récupération d'un event par son id
        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/details/{id}', name: 'details', requirements: ['id' => '\d+'])]
 public function detail(EventRepository $eventRepository, int $id):Response{
        $event = $eventRepository->find($id);
        if(!$event){
            //Lance une erreur 404
            throw $this->createNotFoundException('event not found');
        }
        return $this->render('event/EventDetails.html.twig', [
            'event' => $event,
        ]);
    }


    #[Route('/add', name: 'create')]
public function create(
                 EntityManagerInterface $entityManager,
                 Request $request,
                 UserRepository $userRepository,
                 StateEventRepository $stateEventRepository,

    ):Response
    {
        $statesEvent = $stateEventRepository->findAll();
        $stateEventCreated=$statesEvent[0]; // Sortie Etat "Created"
        $user = $userRepository->find($this->getUser()->getId());
        $event = new Event();
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        if($eventForm->isSubmitted() && $eventForm->isValid()){
            $event->setOrganizer($this->getUser());
            $event->addParticipant($user);
            $event->setStateEvent($stateEventCreated);

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', "Sortie ajoutée !");
            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/addEvent.html.twig', [
            'eventForm' => $eventForm
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
      public function update(
          EntityManagerInterface $entityManager,
          EventRepository $eventRepository,
          Request $request,
          int $id
    ):Response
    {
        $event = $eventRepository->find($id);
        if(!$event){
            throw $this->createNotFoundException("La sortie n'a pas été trouvé");
        }
        if($event->getOrganizer() !== $this->getUser()){
            throw $this->createNotFoundException("Vous n'êtes pas l'organisateur, vous ne pouvez pas modifier cette sortie");
        }

        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        if($eventForm->isSubmitted() && $eventForm->isValid()){
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', "sortie modifiée");
            return $this->redirectToRoute('event_list');
        }
        return $this->render('event/updateEvent.html.twig', [
            'eventForm' => $eventForm
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
        public function delete(
            EntityManagerInterface $entityManager,
            EventRepository $eventRepository,
            Request $request,
            int $id
    ):Response
    {
        $event = $eventRepository->find($id);
        if(!$event){
            throw $this->createNotFoundException("Cette sortie n'a pas été trouvée");
        }
        if($event->getOrganizer() !== $this->getUser()){
            throw $this->createNotFoundException("Vous n'êtes pas l'organisateur, vous ne pouvez pas supprimer cette sortie");
        }
        $entityManager->remove($event);
        $entityManager->flush();

        $this->addFlash( 'success' ,'Sortie supprimée !!');
        return $this->redirectToRoute('event_list');
    }
    #[Route('/join/{id}', name: 'join')]
    public function join(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        UserRepository $userRepository,
        StateEventRepository $stateEventRepository,
        Request $request,
        int $id
    ):Response
    {
        $statesEvent = $stateEventRepository->findAll();
        $stateEventOpen=$statesEvent[1]; // Evenement "Open"
        $user = $userRepository->find($this->getUser()->getId()); // Utilisateur connecté
        $event = $eventRepository->find($id);
        if(!$event){
            throw $this->createNotFoundException("Cette sortie n'a pas été trouvée");
        }
        if($event->getOrganizer() == $this->getUser()){
            throw $this->createNotFoundException("Vous êtes l'organisateur, vous participez déjà à cette sortie");
        }
        if($event->getParticipants()->contains($user)){
            throw $this->createNotFoundException("Vous êtes déjà inscrit à cette sortie");
        }

        if($event->getParticipants()->count() == $event->getMaxParticipants()){
            throw $this->createNotFoundException("Cette sortie est complète");
        }
        if($event->getDateLine() < new \DateTime('now')){
            throw $this->createNotFoundException("Cette sortie est déjà terminée");
        }
        if($event->getStateEvent() !== $stateEventOpen){
            throw $this->createNotFoundException("Les inscriptions pour cette sortie ne sont pas ouvertes");
        }

        $event->addParticipant($user);

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', "Vous êtes bien inscrit à");

            return $this->redirectToRoute('event_list');
       }



    #[Route('/unsubscribe/{id}', name: 'unsubscribe')]
    public function unsubscribe(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        UserRepository $userRepository,
        int $id
    ): Response
    {
        $user = $userRepository->find($this->getUser()->getId());
        $event = $eventRepository->find($id);
        if (!$event) {
            throw $this->createNotFoundException("Cette sortie n'a pas été trouvée");
        }
        if (!$event->getParticipants()->contains($user)) {
            throw $this->createNotFoundException("Vous n'êtes pas inscrit à cette sortie");
        }
        $event->removeParticipant($user);
        $entityManager->persist($event);
        $entityManager->flush();
        $this->addFlash('success', "Vous êtes bien désinscrit de cette sortie");
        return $this->redirectToRoute('event_list');
    }

}

