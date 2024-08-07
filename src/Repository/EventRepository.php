<?php
namespace App\Repository;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }
    public function findByFilters(array $filters, $user)
    {
        $qb = $this->createQueryBuilder('e');
        if (!empty($filters['campus'])) {
            $qb->andWhere('e.campus = :campus')
                ->setParameter('campus', $filters['campus']);
        }
        if (!empty($filters['search'])) {
            $qb->andWhere('e.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['startDate'])) {
            $qb->andWhere('e.startDate >= :startDate')
                ->setParameter('startDate', $filters['startDate']);
        }
        if (!empty($filters['dateLine'])) {
            $qb->andWhere('e.startDate <= :dateLine')
                ->setParameter('dateLine', $filters['dateLine']);
        }
        if (!empty($filters['organisateur'])) {
            $qb->andWhere('e.organizer = :organizer')
                ->setParameter('organizer', $user);
        }
        if (!empty($filters['inscrit'])) {
            $qb->andWhere(':user MEMBER OF e.participants')
                ->setParameter('user', $user);
        }
        if (!empty($filters['non_inscrit'])) {
            $qb->andWhere(':user NOT MEMBER OF e.participants')
                ->setParameter('user', $user);
        }
        if (!empty($filters['passees'])) {
            $qb->andWhere('e.startDate < :now')
                ->setParameter('now', new \DateTime());
        }
//        $oneMonthAgo = (new \DateTime())->modify('-1 month');
//        if (!empty($filters['archivedDate'])) {
//            $qb->andWhere('e.startDate < :oneMonthAgo')
//                ->setParameter('oneMonthAgo', $oneMonthAgo);
//        }
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Event[] Returns an array of Event objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//    public function findOneBySomeField($value): ?Event
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
