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


//EXPLICATION createQUERYBuilder : traduction en SQL simple
/*SELECT *
FROM orders o
WHERE o.user_id = :user
AND o.status IN ('paid', 'delivered')
ORDER BY o.date DESC;
Explications :
o.user = :user → en SQL, c’est souvent la colonne user_id.

o.status IN (:statuses) → en SQL, on remplace par la liste des statuts qu'on veut ('paid', 'delivered').
ORDER BY o.date DESC → même logique en SQL.
    */




}
