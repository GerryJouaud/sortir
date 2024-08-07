<?php
namespace App\Controller;
use App\Entity\Event;
use App\Form\EventType;
use App\Repository\CampusRepository;
use App\Repository\EventRepository;
use App\Repository\PlaceRepository;
use App\Repository\StateEventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/event', name: 'event_')]
class EventController extends AbstractController
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
        // On récupère chacun des Start Date pour vérifier si elles ont dépassée 1 mois, si oui elles sont "archived"
        $allEvents = $eventRepository->findAll();
        $statesEvent = $stateEventRepository->findAll();
        $stateEventArchived=$statesEvent[6]; // Sortie Etat "Archived"
        $limitArchived=new \DateTime();
        $limitArchived->modify('-1 month');

        foreach($allEvents as $event ){
            if($event->getStartDate() <= $limitArchived && $event->getStateEvent() != $stateEventArchived ){
                $event->setStateEvent($stateEventArchived);
            }
        }


        // On récupère tous les campus

        $allCampus = $campusRepository->findAll();
        // On récupère les filtres depuis la requête
        $filters = [
            'campus' => $request->query->get('campus'),
            'search' => $request->query->get('search'),
            'startDate' => $request->query->get('start_date'),
            'dateLine' => $request->query->get('date_line'),
            'organisateur' => $request->query->get('organisateur'),
            'inscrit' => $request->query->get('inscrit'),
            'non_inscrit' => $request->query->get('non_inscrit'),
            'passees' => $request->query->get('passees'),
        ];

        // Conversion des dates en string si présentes
        if ($filters['startDate']) {
            $filters['startDate'] = \DateTime::createFromFormat('Y-m-d H:i:s', $filters['startDate'] . ' 00:00:00');
        }
        if ($filters['dateLine']) {
            $filters['dateLine'] = \DateTime::createFromFormat('Y-m-d H:i:s', $filters['dateLine'] . ' 23:59:59');
        }

        // On récupère les événements filtrés
        $events = $eventRepository->findByFilters($filters, $this->getUser());

        // On rend la vue avec les événements, les campus et les filtres
        return $this->render('main/index.html.twig', [
            'events' => $events,
            'allCampus' => $allCampus,
            'filters' => $filters,
        ]);
    }

    #[Route('/details/{id}', name: 'details', requirements: ['id' => '\d+'])]
    public function detail(EventRepository $eventRepository, UserRepository $userRepository, int $id):Response{
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', "Vous devez être connecté pour afficher une sortie !");
            return $this->redirectToRoute('user_login'); // Redirige vers la page de connexion
        }
        $event = $eventRepository->find($id);
        if(!$event){
            //Lance une erreur 404
            throw $this->createNotFoundException('event not found');
        }
        return $this->render('event/EventDetails.html.twig', [
            'event' => $event,
            'user' => $user,
        ]);
    }

    #[Route('/add', name: 'create')]
    public function create(
        EntityManagerInterface $entityManager,
        Request $request,
        UserRepository $userRepository,
        StateEventRepository $stateEventRepository,
    ):Response
    {
        $today = new \DateTime();
        $statesEvent = $stateEventRepository->findAll();
        $stateEventCreated=$statesEvent[0]; // Sortie Etat "Created"
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', "Vous devez être connecté pour créer une sortie !");
            return $this->redirectToRoute('user_login'); // Redirige vers la page de connexion
        }
        $event = new Event();
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        if($eventForm->isSubmitted() && $eventForm->isValid()){
            $event->setOrganizer($this->getUser());
            $event->addParticipant($user);
            $event->setStateEvent($stateEventCreated);

            if($event->getStartDate() < $today ){
                $this->addFlash('danger', "La date de début ne peut pas être antiérieure à ajourd'hui ! !");
                return $this->redirectToRoute('home');
            }

            if($event->getDateLine() < $today ){
                $this->addFlash('danger', "La cloture des inscriptions ne peut pas être antiérieure à ajourd'hui ! ");
                return $this->redirectToRoute('home');
            }

            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', "Sortie ajoutée !");
            return $this->redirectToRoute('home');
        }
        return $this->render('event/addEvent.html.twig', [
            'eventForm' => $eventForm
        ]);
    }
    #[Route('/update/{id}', name: 'update')]
    public function update(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        Request $request,
        int $id
    ):Response
    {
        $user=$this->getUser();
        $event = $eventRepository->find($id);
        if(!$event){
            throw $this->createNotFoundException("La sortie n'a pas été trouvé");
        }
        if($user->getRoles()[0]=='ROLE_ADMIN'){
            $eventForm = $this->createForm(EventType::class, $event);
            $eventForm->handleRequest($request);
            if($eventForm->isSubmitted() && $eventForm->isValid()){
                $entityManager->persist($event);
                $entityManager->flush();
                $this->addFlash('success', "Modifications ajoutées");
                return $this->redirectToRoute('home');
            }
            return $this->render('event/updateEvent.html.twig', [
                'eventForm' => $eventForm]);
        }
        if($event->getOrganizer() !== $this->getUser()){
            throw $this->createNotFoundException("Vous n'êtes pas l'organisateur, vous ne pouvez pas modifier cette sortie");
        }
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        if($eventForm->isSubmitted() && $eventForm->isValid()){
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', "Modifications ajoutées");
            return $this->redirectToRoute('home');
        }
        return $this->render('event/updateEvent.html.twig', [
            'eventForm' => $eventForm
        ]);
    }
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        Request $request,
        int $id
    ):Response
    {
        $user = $this->getUser();
        $event = $eventRepository->find($id);
        if(!$event){
            $this->addFlash('danger', "Sortie non trouvée !");
            return $this->redirectToRoute('home');
        }
        if($user->getRoles()[0]=='ROLE_ADMIN'){
            $entityManager->remove($event);
            $entityManager->flush();
            $this->addFlash( 'success' ,'Sortie supprimée !!');
            return $this->redirectToRoute('home');
        }
        if($event->getOrganizer() !== $this->getUser()  ){
            $this->addFlash('danger', "Vous n'êtes pas l'organisateur, vous ne pouvez pas supprimer cette sortie !");
            return $this->redirectToRoute('home');
        }

        $entityManager->remove($event);
        $entityManager->flush();
        $this->addFlash( 'success' ,'Sortie supprimée !!');
        return $this->redirectToRoute('home');
    }
    #[Route('/join/{id}', name: 'join')]
    public function join(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        UserRepository $userRepository,
        StateEventRepository $stateEventRepository,
        Request $request,
        int $id
    ):Response
    {
        $statesEvent = $stateEventRepository->findAll();
        $stateEventOpen=$statesEvent[1]; // Evenement "Open"
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', "Vous devez être connecté pour rejoindre une sortie !");
            return $this->redirectToRoute('user_login'); // Redirige vers la page de connexion
        }
        $user = $userRepository->find($this->getUser()->getId()); // Utilisateur connecté
        $event = $eventRepository->find($id);
        if(!$event){
            $this->addFlash('danger',"Cette sortie n'a pas été trouvée !");
            return $this->redirectToRoute('home');
        }
        if($event->getOrganizer() == $this->getUser()){
            $this->addFlash('danger',"Vous êtes l'organisateur, vous participez déjà à cette sortie !");
            return $this->redirectToRoute('home');
        }
        if($event->getParticipants()->contains($user)){
            $this->addFlash('danger',"Vous êtes déjà inscrit à cette sortie !");
            return $this->redirectToRoute('home');
        }
        if($event->getParticipants()->count() == $event->getMaxParticipants()){
            $this->addFlash('danger',"Cette sortie est complète !");
            return $this->redirectToRoute('home');
        }
        if($event->getDateLine() < new \DateTime('now')){
            $this->addFlash('danger',"Cette sortie est déjà terminée !");
            return $this->redirectToRoute('home');
        }
        if($event->getStateEvent() !== $stateEventOpen){
            $this->addFlash('danger', "Les inscriptions pour cette sortie ne sont pas ouvertes !");
            return $this->redirectToRoute('home');
        }
        $event->addParticipant($user);
        $entityManager->persist($event);
        $entityManager->flush();
        $this->addFlash('success', "Vous êtes bien inscrit à la sortie !");
        return $this->redirectToRoute('home');
    }

    #[Route('/unsubscribe/{id}', name: 'unsubscribe')]
    public function unsubscribe(
        EntityManagerInterface $entityManager,
        EventRepository $eventRepository,
        UserRepository $userRepository,
        int $id
    ): Response
    {
        $user = $userRepository->find($this->getUser()->getId());
        $event = $eventRepository->find($id);
        if (!$event) {
            $this->addFlash('danger', "Cette sortie n'a pas été trouvée !");
            return $this->redirectToRoute('home');
        }
        if (!$event->getParticipants()->contains($user)) {
            $this->addFlash('danger', "Vous n'êtes pas inscrit à cette sortie !");
            return $this->redirectToRoute('home');
        }
        if ($event->getStartDate() < new \DateTime('now') ) {
            $this->addFlash('danger', "La sortie a débutée, vous ne pouvez vous désinscrire !");
            return $this->redirectToRoute('home');
        }
        if ($event->getOrganizer() == $this->getUser()) {
            $this->addFlash('danger', "Vous organisez cette sortie, vous ne pouvez pas vous désinscire !");
            return $this->redirectToRoute('home');
        }
        $event->removeParticipant($user);
        $entityManager->persist($event);
        $entityManager->flush();
        $this->addFlash('success', "Vous êtes bien désinscrit de cette sortie");
        return $this->redirectToRoute('home');
    }
}



