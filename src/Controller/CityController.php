<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/city', name: 'city_')]
class CityController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(EntityManagerInterface $entityManager, CityRepository $cityRepository): Response
    {

        $cities = $cityRepository->findAll();

        return $this->render('city/city.html.twig', [
            "cities" => $cities,
        ]);
    }

    #[Route('/details{id}', name: 'details', requirements: ['id' => '\d+'])]
    public function details(CityRepository  $cityRepository, int $id): Response{
    $city = $cityRepository->find($id);
        if (!$city) {
            throw $this->createNotFoundException("Cette ville n'a pas été trouvée");
        }
        return $this->render('city/cityDetails.html.twig', [
            "city" => $city,
        ]);
    }

    #[Route('/add', name: 'create')]
    public function create(
        EntityManagerInterface $entityManager,
        Request                $request
    ): Response
    {
        $city = new City();
        $cityForm = $this->createForm(CityType::class, $city);
        $cityForm->handleRequest($request);

        if ($cityForm->isSubmitted() && $cityForm->isValid()) {
            $entityManager->persist($city);
            $entityManager->flush();

            $this->addFlash('success', 'Ville ajoutée!');
            return $this->redirectToRoute('city_list');
        }

        return $this->render('city/addCity.html.twig', [
            "cityForm" => $cityForm

        ]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function update(EntityManagerInterface $entityManager, Request $request, CityRepository $cityRepository, int $id): Response
    {
        $city = $cityRepository->find($id);
        if (!$city) {
            throw $this->createNotFoundException("Cette ville n'a pas été trouvée");
        }
        $cityForm = $this->createForm(CityType::class, $city);
        $cityForm->handleRequest($request);
        if ($cityForm->isSubmitted() && $cityForm->isValid()) {
            $entityManager->persist($city);
            $entityManager->flush();

            $this->addFlash("success", "Ville modifiée");
            return $this->redirectToRoute('city_list');
        }
        return $this->render('city/updateCity.html.twig', [
            "cityForm" => $cityForm
        ]);

    }
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(EntityManagerInterface $entityManager,  CityRepository $cityRepository, int $id): Response    {
        $city = $cityRepository->find($id);
        if (!$city) {
            throw $this->createNotFoundException("Cette ville n'a pas été trouvée");
        }
        $entityManager->remove($city);
        $entityManager->flush();

        $this->addFlash('success', 'Ville supprimée!');
        return $this->redirectToRoute('city_list');

    }
}
