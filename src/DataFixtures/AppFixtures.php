<?php

namespace App\DataFixtures;



// Importation des entités nécessaires
use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\StateEvent;
use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Repository\PlaceRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture{

    private readonly Generator $faker;
    private UserPasswordHasherInterface $userPasswordHasher;

     //Constructeur pour initialiser le hasher de mots de passe


    public function __construct(UserPasswordHasherInterface $userPasswordHasher,
                                private CampusRepository $campusRepository,
                                private PlaceRepository $placeRepository,
                                private EventRepository $eventRepository,
                                private UserRepository $userRepository,
    )
    {
        $this-> faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager ):void
    {
        $this->addCampus($manager);
        $this->addCity($manager);
        $this->addStateEvent($manager);
        $this->addPlace($manager);
        $this->addUsers(50, $manager);
        $this->addEvent($manager);



    }
    public function addUsers(int $number,ObjectManager $manager)
    {
        // Récupération de tous les campus pour assignation aléatoire
         $allCampus = $this->campusRepository->findAll();
        // Création de 50 utilisateurs aléatoires
        for ($i = 0; $i < $number; $i++) {
            $user = new User();
            $user
                ->setFirstName($this->faker->firstName)
                ->setLastName($this->faker->lastName())
                ->setEmail($this->faker->email())
                ->setPhone($this->faker->phoneNumber)
                ->setState($this->faker->boolean)
                ->setPassword($this->faker->password())
                ->setCampus($this->faker->randomElement($allCampus))
                ->setRoles(['ROLE_USER'])
                ->setPoster('image.jpg');



            $manager->persist($user);
        }

        $manager->flush();

    }

    public function addCampus(ObjectManager $manager){

        // Fonction pour ajouter des campus
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


    public function addCity(ObjectManager $manager)
    {
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


    public  function addPlace(ObjectManager $manager ){
        // Récupération de toutes les villes pour assignation aléatoire
        for ($i = 0 ; $i<10; $i++){
            $place = new Place();
            $place
                ->setName($this->faker->word)
                ->setStreet($this->faker->streetAddress)
                ->setLatitude($this->faker->latitude)
                ->setLongitude($this->faker->longitude)
                ->setCity($this->faker->randomElement($manager->getRepository(City::class)->findAll()));
            $manager->persist($place);

        }

        $manager->flush();
    }


    // Fonction pour ajouter des événements
    public function addEvent(ObjectManager $manager)
    {

        // Création de 10 événements aléatoires
        for($i = 0; $i < 10; $i++){
            $faker = Factory::create('fr_FR');

            $event = new Event();
            $event
                ->setName($faker->word)
                ->setStartDate($faker->dateTimeBetween("-1 month", "+6 month"))
                ->setDuration($faker->numberBetween(60,240))
                ->setDateLine($faker->dateTimeBetween("1 month", "+6 month"))
                ->setMaxParticipants($faker->numberBetween(1,10))
                ->setDescription($faker->paragraph())
                ->setCampus($faker->randomElement($manager->getRepository(Campus::class)->findAll()))
                ->setPlace($faker->randomElement($manager->getRepository(Place::class)->findAll()))
                ->setStateEvent($faker->randomElement($manager->getRepository(StateEvent::class)->findAll()))
                ->setOrganizer($faker->randomElement($manager->getRepository(User::class)->findAll()));

            $manager->persist($event);

        }
        $manager->flush();

    }


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


