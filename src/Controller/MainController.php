<?php

namespace App\Controller;

use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Repository\PlaceRepository;
use App\Repository\StateEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function list(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        PlaceRepository $placeRepository,
        StateEventRepository $stateEventRepository,
        Request $request,
        CampusRepository $campusRepository

    ): Response
    {

        //Récupération d'un event par son id
        return $this->redirectToRoute("event_list");
    }
}