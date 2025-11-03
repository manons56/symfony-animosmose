<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }


    public function findByPriceRange(float $min, float $max): array
    {
        return $this->createQueryBuilder('p') // SELECT * FROM products p
        ->distinct() // avoid duplicates
        ->leftjoin('p.variants', 'v') // LEFT JOIN includes all products, even those without variants
        // Connects each product to its variants.
        // v is the alias for variants.
        // The product column corresponds to the OneToMany relation in Products.
        ->andWhere('v.price BETWEEN :min AND :max OR v.id IS NULL') // WHERE v.price BETWEEN :min AND :max
        ->setParameter('min', $min)
            ->setParameter('max', $max)
            // Here we replace the parameters with their values
            // For example, if $min = 10 and $max = 50
            // WHERE v.price BETWEEN 10 AND 50
            ->getQuery()
            ->getResult();
    }

    /*public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('p') // SELECT * FROM products p
            ->orderBy('p.id', 'DESC') // ORDER BY p.id DESC
            ->setMaxResults($limit) // LIMIT 5
            ->getQuery() // transforms the query builder into a Doctrine Query object
            ->getResult(); // executes the query and returns the corresponding Products entities in an array
    }
*/
}



// Protection against SQL injections:
// when doing, for example, a SELECT...FROM...WHERE... :email --> :email is a named parameter
// Doctrine sends the SQL query to the engine without the value (handled separately), automatically escaping it, preventing any injection
// if we make a non-parameterized query, the code is injected into the query and there is a risk of SQL injection


// We write a DQL (Doctrine Query Language) query --> Doctrine understands it
// --> Doctrine translates it into raw SQL by replacing entity and field names
// Doctrine replaces parameters like :email with ? and keeps the real values separate
// Doctrine executes via PDO (prepare + execute), which protects against SQL injections
