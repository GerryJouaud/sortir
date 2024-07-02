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

        if ($filters['campus']) {
            $qb->andWhere('e.campus = :campus')
                ->setParameter('campus', $filters['campus']);
        }
        if ($filters['search']) {
            $qb->andWhere('e.name LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }
        if ($filters['startDate']) {
            $qb->andWhere('e.startDate >= :startDate')
                ->setParameter('startDate', $filters['startDate']);
        }
        if ($filters['dateLine']) {
            $qb->andWhere('e.dateLine <= :dateLine')
                ->setParameter('dateLine', $filters['dateLine']);
        }
        if ($filters['organisateur']) {
            $qb->andWhere('e.organizer = :organizer')
                ->setParameter('organizer', $user);
        }
        if ($filters['inscrit']) {
            $qb->andWhere(':user MEMBER OF e.participants')
                ->setParameter('user', $user);
        }
        if ($filters['non_inscrit']) {
            $qb->andWhere(':user NOT MEMBER OF e.participants')
                ->setParameter('user', $user);
        }
        if ($filters['passees']) {
            $qb->andWhere('e.startDate < :now')
                ->setParameter('now', new \DateTime());
        }
        if ($filters['notArchived']) {
            $qb->andWhere('e.startDate > :now')
                ->setParameter('now', new \DateTime());
        }


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
