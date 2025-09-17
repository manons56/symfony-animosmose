<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Enum\OrderStatus;

/**
 * @extends ServiceEntityRepository<Orders>
 */
class OrdersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Orders::class);
    }

    public function findCompletedOrdersForUser(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('statuses', [OrderStatus::Paid, OrderStatus::Delivered])
            ->orderBy('o.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Orders[] Returns an array of Orders objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Orders
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
