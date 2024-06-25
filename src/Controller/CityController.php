<?php

namespace App\Controller;

use App\Entity\City;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/city', name: 'city_')]
class CityController extends AbstractController
{
    #[Route('/add', name: 'create')]
    public function create(EntityManagerInterface $entityManager): Response
    {
        $Rennes=new City();
        $Rennes->setName("Rennes");
        $Rennes->setZipCode("3500");
        $entityManager->persist($Rennes);
        $entityManager->flush();

        return $this->render('city/city.html.twig', [

        ]);
    }
}
