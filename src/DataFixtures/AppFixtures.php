<?php

namespace App\DataFixtures;



use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Place;
use App\Entity\StateEvent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


class AppFixtures extends Fixture{

    private readonly Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $this->addCity($manager);
        $this->addCampus($manager);
        $this->addStateEvent($manager);
        $this->addPlace($manager, $faker);
        $this->addUser($manager, $faker);
        $this->addEvent($manager, $faker);

       $manager->flush();
    }

    public function addCampus(ObjectManager $manager)
    {
        $campusRennes = new Campus();
        $campusRennes
            ->setName("Rennes");
        $manager->persist($campusRennes);

        $campusNantes = new Campus();
        $campusNantes
            ->setName("Nantes");
        $manager->persist($campusNantes);

        $campusQuimper = new Campus();
        $campusQuimper
            ->setName("Quimper");
        $manager->persist($campusQuimper);

        $campusNiort = new Campus();
        $campusNiort
            ->setName("Niort");
        $manager->persist($campusNiort);

        $manager->flush();

    }
    public function addCity(ObjectManager $manager){
        $cityRennes = new City();
        $cityRennes
            ->setName("Rennes")
            ->setZipCode("35000");
        $manager->persist($cityRennes);

        $cityNantes = new City();
        $cityNantes
            ->setName("Nantes")
            ->setZipCode("44000");
        $manager->persist($cityNantes);

        $cityQuimper = new City();
        $cityQuimper
            ->setName("Quimper")
            ->setZipCode("29000");
        $manager->persist($cityQuimper);

        $cityNiort = new City();
        $cityNiort
            ->setName("Niort")
            ->setZipCode("79000");
        $manager->persist($cityNiort);

        $manager->flush();

    }

    public function addEvent(ObjectManager $manager, Generator $generator)
    {
        $campus = $manager->getRepository(Campus::class)->findAll();
        $place = $manager->getRepository(Place::class)->findAll();
        $stateEvent = $manager->getRepository(StateEvent::class)->findAll();


    }

    public function addStateEvent(ObjectManager $manager)
    {
        $stateEventCreated = new State();
        $stateEventCreated
            ->setLabel("created");
        $manager->persist($stateEventCreated);

        $stateEventOpen = new State();
        $stateEventOpen
            ->setLabel("open");
        $manager->persist($stateEventOpen);

        $stateEventClosed = new State();
        $stateEventClosed
            ->setLabel("closed");
        $manager->persist($stateEventClosed);

        $stateEventInProgress = new State();
        $stateEventInProgress
            ->setLabel("inProgress");
        $manager->persist($stateEventInProgress);

        $stateEventFinished = new State();
        $stateEventFinished
            ->setLabel("finished");
        $manager->persist($stateEventFinished);

        $stateEventCanceled = new State();
        $stateEventCanceled
            ->setLabel("canceled");
        $manager->persist($stateEventCanceled);

        $stateEventArchived = new State();
        $stateEventArchived
            ->setLabel("archived");
        $manager->persist($stateEventArchived);

        $manager->flush();

    }
}


