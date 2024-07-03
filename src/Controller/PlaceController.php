<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Place;
use App\Form\CityType;
use App\Form\PlaceType;
use App\Repository\CityRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/place', name: 'place_')]
class PlaceController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(EntityManagerInterface $entityManager, PlaceRepository $placeRepository): Response
    {

        $places = $placeRepository->findAll();

        return $this->render('place/placeList.html.twig', [
            "places" => $places,
        ]);
    }

    #[Route('/add', name: 'create')]
    public function create(
        EntityManagerInterface $entityManager,
        Request                $request
    ): Response
    {
        $place = new Place();
        $placeForm = $this->createForm(PlaceType::class, $place);
        $placeForm->handleRequest($request);

        if ($placeForm->isSubmitted() && $placeForm->isValid()) {
            $entityManager->persist($place);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu ajouté!');
            return $this->redirectToRoute("event_create");
        }
        return $this->render('place/addPlace.html.twig', [
            "placeForm" => $placeForm]);
    }

    #[Route('/details{id}', name: 'details', requirements: ['id' => '\d+'])]
    public function details(PlaceRepository $placeRepository, int $id): Response
    {
        $place = $placeRepository->find($id);
        if (!$place) {
            throw $this->createNotFoundException("Ce lieu n'a pas été trouvé");
        }
        return $this->render('place/placeDetails.html.twig', [
            "place" => $place,
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(EntityManagerInterface $entityManager, Request $request, PlaceRepository $placeRepository, int $id): Response
    {
        $place = $placeRepository->find($id);
        if (!$place) {
            throw $this->createNotFoundException("Ce lieu n'a pas été trouvée");
        }
        $placeForm = $this->createForm(PlaceType::class, $place);
        $placeForm->handleRequest($request);
        if ($placeForm->isSubmitted() && $placeForm->isValid()) {
            $entityManager->persist($place);
            $entityManager->flush();

            $this->addFlash("success", "Lieu modifié");
            return $this->redirectToRoute("place_list");
        }
        return $this->render('place/placeUpdate.html.twig', [
            "placeForm" => $placeForm
        ]);

    }
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(EntityManagerInterface $entityManager,  PlaceRepository $placeRepository, int $id): Response    {
        $place = $placeRepository->find($id);
        if (!$place) {
            throw $this->createNotFoundException("Ce lieu n'a pas été trouvé");
        }
        $entityManager->remove($place);
        $entityManager->flush();

        $this->addFlash('success', 'Lieu supprimé!');
        return $this->redirectToRoute('place_list');

    }

}