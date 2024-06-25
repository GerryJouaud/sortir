<?php

namespace App\DataFixtures;



// Importation des entités nécessaires
use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\StateEvent;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture{

    private readonly Generator $faker;
    private UserPasswordHasherInterface $passwordHasher;

     //Constructeur pour initialiser le hasher de mots de passe
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // Création d'un générateur Faker pour les données aléatoires en français
        $faker = Factory::create('fr_FR');
        // Appel des différentes fonctions d'ajout de données
        $this->addCity($manager);
        $this->addCampus($manager);
        $this->addStateEvent($manager);
        $this->addPlace($manager, $faker);
        $this->addUser($manager, $faker);
        $this->addEvent($manager, $faker);

        // Envoi des modifications à la base de données
        $manager->flush();
    }
    public function addUser(ObjectManager $manager, Generator $generator)
    {
        // Récupération de tous les campus pour assignation aléatoire
        $campus = $manager->getRepository(Campus::class)->findAll();

        // Création de 50 utilisateurs aléatoires
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

    // Fonction pour ajouter des campus
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

    // Fonction pour ajouter des villes
    public function addCity(ObjectManager $manager, Generator $generator){
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

    // Fonction pour ajouter des événements
    public function addEvent(ObjectManager $manager, Generator $generator)
    {
        $campus = $manager->getRepository(Campus::class)->findAll();
        $place = $manager->getRepository(Place::class)->findAll();
        $stateEvent = $manager->getRepository(StateEvent::class)->findAll();
        $organizer = $manager ->getRepository(User::class)->findAll();

        // Création de 10 événements aléatoires
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

        $cities = $manager ->getRepository(City::class)->findAll();
        dd($cities);
    }

    // Fonction pour ajouter des lieux
    public  function addPlace(ObjectManager $manager , Generator $generator){
        // Récupération de toutes les villes pour assignation aléatoire
        $cities = $manager ->getRepository(City::class)->findAll();
        for ($i = 0 ; $i<10; $i++){
            $place = new Place();
            $place
                ->setName($generator->word)
                ->setStreet($generator->streetAddress)
                ->setLatitude($generator->latitude)
                ->setLongitude($generator->longitude)
                ->setCity($generator->randomElement($cities));
            // Persistance du lieu
            $manager->persist($place);

        }

        $manager->flush();
    }

    // Fonction pour ajouter des états d'événement
    public function addStateEvent(ObjectManager $manager)
    {
        // Création et persistance de chaque état d'événement
        $stateEventCreated = new StateEvent();
        $stateEventCreated
            ->setWording("created");
        $manager->persist($stateEventCreated);

        $stateEventOpen = new StateEvent();
        $stateEventOpen
            ->setWording("open");
        $manager->persist($stateEventOpen);

        $stateEventClosed = new StateEvent();
        $stateEventClosed
            ->setWording("closed");
        $manager->persist($stateEventClosed);

        $stateEventInProgress = new StateEvent;
        $stateEventInProgress
            ->setWording("inProgress");
        $manager->persist($stateEventInProgress);

        $stateEventFinished = new StateEvent;
        $stateEventFinished
            ->setWording("finished");
        $manager->persist($stateEventFinished);

        $stateEventCanceled = new StateEvent;
        $stateEventCanceled
            ->setWording("canceled");
        $manager->persist($stateEventCanceled);

        $stateEventArchived = new  StateEvent;
        $stateEventArchived
            ->setWording("archived");
        $manager->persist($stateEventArchived);

        $manager->flush();

    }
}


