<?php

namespace App\DataFixtures;



use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\StateEvent;
use Couchbase\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;


class AppFixtures extends Fixture{

    private readonly Generator $faker;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

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
    public function addUser(ObjectManager $manager, Generator $generator)
    {
        $campus = $manager->getRepository(Campus::class)->findAll();

        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user
                ->setUserName($generator->userName)
                ->setRoles(["ROLE_USER"]);
            $password = $this->passwordHasher->hashPassword($user,'1234');
            $user
                ->setPassword($password)
                ->setLastName($generator->lastName)
                ->setFirstName($generator->firstName)
                ->setPhone($generator->phoneNumber)
                ->setEmail($generator->email)
                ->setState($generator->boolean())
                ->setCampus($generator->randomElement($campus));
            $manager->persist($user);
        }

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
        $organizer = $manager ->getRepository(User::class)->findAll();

        for($i = 0; $i < 10; $i++){
            $event = new Event();
            $event
                ->setName($generator->word)
                ->setStartDate($generator->dateTimeBetween('-1 month', '+6 month'))
                ->setDuration($generator->numberBetween(30,240))
                ->setDateLine($generator->dateTimeBetween("-1 month", " +6 month"))
                ->setMaxParticipants($generator->numberBetween(5,50))
                ->setDescription($generator->text)
                ->setCampus($generator->randomElement($campus))
                ->setPlace($generator->randomElement($place))
                ->setStateEvent($generator->randomElement($stateEvent))
                ->setOrganizer($organizer->randomElement($organizer));
            $manager->persist($event);
        }

        $manager->flush();
    }

    public  function addPlace(ObjectManager $manager){
        $cities = $manager ->getRepository(City::class)->findAll();
        for ($i = 0 ; $i<10; $i++){
            $place = new Place();
            $place
                ->setName($generator->word)
                ->setStreet($generator->streetAddress)
                ->setLatitude($generator->latitude)
                ->setLongitude($generator->longitude)
                ->setCity($generator->randomElement($cities));
            $manager->persist($place);

        }

        $manager->flush();
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


