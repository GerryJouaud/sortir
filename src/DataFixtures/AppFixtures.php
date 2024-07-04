<?php

namespace App\DataFixtures;

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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private CampusRepository $campusRepository;
    private PlaceRepository $placeRepository;
    private EventRepository $eventRepository;
    private UserRepository $userRepository;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        CampusRepository $campusRepository,
        PlaceRepository $placeRepository,
        EventRepository $eventRepository,
        UserRepository $userRepository
    ) {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->campusRepository = $campusRepository;
        $this->placeRepository = $placeRepository;
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $this->addCampus($manager);
        $this->addCity($manager);
        $this->addStateEvent($manager);
        $this->addPlace($manager);
        $this->addUsers($manager);
        $this->addEvent($manager);
    }

    private function addUsers(ObjectManager $manager)
    {
        $allCampus = $this->campusRepository->findAll();

        $usersData = [
            ['John', 'Doe', 'john.doe@example.com', '0612345670'],
            ['Jane', 'Smith', 'jane.smith@example.com', '0612345671'],
            ['Paul', 'Brown', 'paul.brown@example.com', '0612345672'],
            ['Anna', 'Johnson', 'anna.johnson@example.com', '0612345673'],
            ['James', 'Williams', 'james.williams@example.com', '0612345674'],
            ['Emily', 'Jones', 'emily.jones@example.com', '0612345675'],
            ['Michael', 'Garcia', 'michael.garcia@example.com', '0612345676'],
            ['Sarah', 'Miller', 'sarah.miller@example.com', '0612345677'],
            ['David', 'Martinez', 'david.martinez@example.com', '0612345678'],
            ['Laura', 'Hernandez', 'laura.hernandez@example.com', '0612345679']
        ];

        foreach ($usersData as $i => $userData) {
            $user = new User();
            $user->setFirstName($userData[0])
                ->setLastName($userData[1])
                ->setEmail($userData[2])
                ->setPhone($userData[3])
                ->setState(true)
                ->setPassword($this->userPasswordHasher->hashPassword($user, 'password'))
                ->setCampus($allCampus[$i % count($allCampus)])
                ->setRoles(['ROLE_USER'])
                ->setPoster('image.jpg');

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function addCampus(ObjectManager $manager)
    {
        $campusNames = ["Rennes", "Nantes", "Quimper", "Niort"];

        foreach ($campusNames as $name) {
            $campus = new Campus();
            $campus->setName($name);
            $manager->persist($campus);
        }

        $manager->flush();
    }

    private function addCity(ObjectManager $manager)
    {
        $cities = [
            ["Rennes", "35000"],
            ["Nantes", "44000"],
            ["Quimper", "29000"],
            ["Niort", "79000"]
        ];

        foreach ($cities as $cityData) {
            $city = new City();
            $city->setName($cityData[0])
                ->setZipCode($cityData[1]);
            $manager->persist($city);
        }

        $manager->flush();
    }

    private function addPlace(ObjectManager $manager)
    {
        $cities = $manager->getRepository(City::class)->findAll();

        $placesData = [
            ['Centre des Congrès', '12 Rue de la Gare', 48.117266, -1.6777926, $cities[0]],
            ['Salle Polyvalente', '45 Avenue Jean Jaurès', 47.218371, -1.553621, $cities[1]],
            ['Parc des Expositions', '101 Rue de Paris', 48.002778, -4.0925, $cities[2]],
            ['Espace Culturel', '50 Boulevard de la Liberté', 46.323722, -0.458744, $cities[3]],
            ['Maison des Associations', '22 Rue de Bretagne', 48.117266, -1.6777926, $cities[0]],
            ['Théâtre Municipal', '5 Place du Théâtre', 47.218371, -1.553621, $cities[1]],
            ['Galerie d\'Art', '8 Rue des Arts', 48.002778, -4.0925, $cities[2]],
            ['Hôtel de Ville', '10 Place de la Mairie', 46.323722, -0.458744, $cities[3]],
            ['Bibliothèque Centrale', '14 Rue des Écoles', 48.117266, -1.6777926, $cities[0]],
            ['Stade Municipal', '20 Rue du Stade', 47.218371, -1.553621, $cities[1]],
        ];

        foreach ($placesData as $placeData) {
            $place = new Place();
            $place->setName($placeData[0])
                ->setStreet($placeData[1])
                ->setLatitude($placeData[2])
                ->setLongitude($placeData[3])
                ->setCity($placeData[4]);
            $manager->persist($place);
        }

        $manager->flush();
    }

    private function addEvent(ObjectManager $manager)
    {
        $campuses = $this->campusRepository->findAll();
        $places = $this->placeRepository->findAll();
        $stateEvents = $manager->getRepository(StateEvent::class)->findAll();
        $users = $this->userRepository->findAll();

        $eventsData = [
            [
                'name' => 'Conférence Tech 2024',
                'startDate' => new \DateTime('2024-09-01 10:00:00'),
                'duration' => 180,
                'dateLine' => new \DateTime('2024-08-25 23:59:59'),
                'maxParticipants' => 100,
                'description' => 'Une conférence sur les dernières avancées technologiques.',
                'campus' => $campuses[0],
                'place' => $places[0],
                'stateEvent' => $stateEvents[1],
                'organizer' => $users[0],
                'participants' => [$users[1], $users[2]],
            ],
            [
                'name' => 'Atelier d\'Art',
                'startDate' => new \DateTime('2024-07-15 14:00:00'),
                'duration' => 120,
                'dateLine' => new \DateTime('2024-07-10 23:59:59'),
                'maxParticipants' => 20,
                'description' => 'Un atelier pour explorer votre côté artistique.',
                'campus' => $campuses[1],
                'place' => $places[1],
                'stateEvent' => $stateEvents[0],
                'organizer' => $users[1],
                'participants' => [$users[3], $users[4]],
            ],
            [
                'name' => 'Festival de Musique',
                'startDate' => new \DateTime('2024-08-20 18:00:00'),
                'duration' => 300,
                'dateLine' => new \DateTime('2024-08-15 23:59:59'),
                'maxParticipants' => 500,
                'description' => 'Un festival avec diverses performances musicales.',
                'campus' => $campuses[2],
                'place' => $places[2],
                'stateEvent' => $stateEvents[1],
                'organizer' => $users[2],
                'participants' => [$users[5], $users[6]],
            ],
            [
                'name' => 'Cours de Cuisine',
                'startDate' => new \DateTime('2024-09-05 09:00:00'),
                'duration' => 90,
                'dateLine' => new \DateTime('2024-09-01 23:59:59'),
                'maxParticipants' => 15,
                'description' => 'Apprenez à cuisiner de délicieux repas.',
                'campus' => $campuses[3],
                'place' => $places[3],
                'stateEvent' => $stateEvents[0],
                'organizer' => $users[3],
                'participants' => [$users[7], $users[8]],
            ],
            [
                'name' => 'Foire Scientifique',
                'startDate' => new \DateTime('2024-10-10 11:00:00'),
                'duration' => 240,
                'dateLine' => new \DateTime('2024-10-05 23:59:59'),
                'maxParticipants' => 200,
                'description' => 'Explorez les dernières découvertes scientifiques.',
                'campus' => $campuses[0],
                'place' => $places[4],
                'stateEvent' => $stateEvents[1],
                'organizer' => $users[4],
                'participants' => [$users[9], $users[0]],
            ],
            [
                'name' => 'Rencontre Littéraire',
                'startDate' => new \DateTime('2024-11-12 15:00:00'),
                'duration' => 180,
                'dateLine' => new \DateTime('2024-11-05 23:59:59'),
                'maxParticipants' => 50,
                'description' => 'Rencontrez et discutez avec des passionnés de littérature.',
                'campus' => $campuses[1],
                'place' => $places[5],
                'stateEvent' => $stateEvents[0],
                'organizer' => $users[5],
                'participants' => [$users[1], $users[2]],
            ],
            [
                'name' => 'Présentation de Startups',
                'startDate' => new \DateTime('2024-12-01 14:00:00'),
                'duration' => 120,
                'dateLine' => new \DateTime('2024-11-25 23:59:59'),
                'maxParticipants' => 30,
                'description' => 'Présentez vos idées de startups à des investisseurs potentiels.',
                'campus' => $campuses[2],
                'place' => $places[6],
                'stateEvent' => $stateEvents[1],
                'organizer' => $users[6],
                'participants' => [$users[3], $users[4]],
            ],
            [
                'name' => 'Retraite de Yoga',
                'startDate' => new \DateTime('2024-07-20 08:00:00'),
                'duration' => 480,
                'dateLine' => new \DateTime('2024-07-15 23:59:59'),
                'maxParticipants' => 25,
                'description' => 'Une journée complète consacrée au yoga et à la méditation.',
                'campus' => $campuses[3],
                'place' => $places[7],
                'stateEvent' => $stateEvents[0],
                'organizer' => $users[7],
                'participants' => [$users[5], $users[6]],
            ],
            [
                'name' => 'Sommet Environnemental',
                'startDate' => new \DateTime('2024-08-25 09:00:00'),
                'duration' => 360,
                'dateLine' => new \DateTime('2024-08-20 23:59:59'),
                'maxParticipants' => 150,
                'description' => 'Discussions sur les solutions aux problèmes environnementaux.',
                'campus' => $campuses[0],
                'place' => $places[8],
                'stateEvent' => $stateEvents[1],
                'organizer' => $users[8],
                'participants' => [$users[7], $users[9]],
            ],
            [
                'name' => 'Exposition de Photographie',
                'startDate' => new \DateTime('2024-09-10 10:00:00'),
                'duration' => 240,
                'dateLine' => new \DateTime('2024-09-05 23:59:59'),
                'maxParticipants' => 80,
                'description' => 'Une exposition de photographies impressionnantes par divers artistes.',
                'campus' => $campuses[1],
                'place' => $places[9],
                'stateEvent' => $stateEvents[0],
                'organizer' => $users[9],
                'participants' => [$users[1], $users[0]],
            ],
        ];

        foreach ($eventsData as $eventData) {
            $event = new Event();
            $event->setName($eventData['name'])
                ->setStartDate($eventData['startDate'])
                ->setDuration($eventData['duration'])
                ->setDateLine($eventData['dateLine'])
                ->setMaxParticipants($eventData['maxParticipants'])
                ->setDescription($eventData['description'])
                ->setCampus($eventData['campus'])
                ->setPlace($eventData['place'])
                ->setStateEvent($eventData['stateEvent'])
                ->setOrganizer($eventData['organizer']);

            foreach ($eventData['participants'] as $participant) {
                $event->addParticipant($participant);
            }

            $manager->persist($event);
        }

        $manager->flush();
    }

    private function addStateEvent(ObjectManager $manager)
    {
        $states = ["créé", "ouvert", "fermé", "en cours", "terminé", "annulé", "archivé"];

        foreach ($states as $state) {
            $stateEvent = new StateEvent();
            $stateEvent->setWording($state);
            $manager->persist($stateEvent);
        }

        $manager->flush();
    }
}