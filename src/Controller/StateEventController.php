<?php

namespace App\Controller;

use App\Entity\StateEvent;
use App\Form\StateEventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/state-event")
 */
class StateEventController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct
    (
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="state_event_index", methods={"GET"})
     */
    public function index(): Response
    {
        $stateEvents = $this->entityManager->getRepository(StateEvent::class)->findAll();

        return $this->render('state_event/index.html.twig', [
            'state_events' => $stateEvents,
        ]);
    }

    /**
     * @Route("/new", name="state_event_new", methods={"GET","POST"})
     */
    public function new(
        Request $request
    ): Response
    {
        $stateEvent = new StateEvent();
        $form = $this->createForm(StateEventType::class, $stateEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($stateEvent);
            $this->entityManager->flush();

            return $this->redirectToRoute('state_event_index');
        }

        return $this->render('state_event/new.html.twig', [
            'state_event' => $stateEvent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="state_event_show", methods={"GET"})
     */
    public function show(StateEvent $stateEvent): Response
    {
        return $this->render('state_event/show.html.twig', [
            'state_event' => $stateEvent,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="state_event_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        StateEvent $stateEvent
    ): Response
    {
        $form = $this->createForm(StateEventType::class, $stateEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('state_event_index');
        }

        return $this->render('state_event/edit.html.twig', [
            'state_event' => $stateEvent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="state_event_delete", methods={"POST"})
     */
    public function delete(
        Request $request,
        StateEvent $stateEvent
    ): Response
    {

        //todo

            $this->entityManager->remove($stateEvent);
            $this->entityManager->flush();
        }

     //   return $this->redirectToRoute('state_event_index');
   // }
}
