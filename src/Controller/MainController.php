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
    #[Route('/', name: 'list')]
    public function list(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        PlaceRepository $placeRepository,
        StateEventRepository $stateEventRepository,
        Request $request,
        CampusRepository $campusRepository

    ): Response
    {

        $allCampus=$campusRepository->findAll();

        $filters = [
            'campus' => $request->query->get('campus'),
            'search' => $request->query->get('search'),
            'startDate' => $request->query->get('start_date'),
            'dateLine' => $request->query->get('dateLine'),
            'organisateur' => $request->query->get('organisateur'),
            'inscrit' => $request->query->get('inscrit'),
            'non_inscrit' => $request->query->get('non_inscrit'),
            'passees' => $request->query->get('passees'),
        ];
        $events = $eventRepository->findByFilters($filters, $this->getUser());

        //Récupération d'un event par son id
        return $this->render('main/index.html.twig', [
            'events' => $events,
            'allCampus' => $allCampus,
            'filters' => $filters,
        ]);
    }
}