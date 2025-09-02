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
        return $this->createQueryBuilder('p') //SELECT * FROM products p
            ->join('p.variants', 'v') //INNER JOIN variants v ON v.product_id = p.id
            //On relie chaque produit à ses variants.
            //v est l’alias pour variants.
            //La colonne product_id correspond à la relation OneToMany de Products.
            ->andWhere('v.price BETWEEN :min AND :max') //WHERE v.price BETWEEN :min AND :max
            ->setParameter('min', $min)
            ->setParameter('max', $max)
        //-- Ici on remplace les paramètres par leurs valeurs
        //Par exemple si $min = 10 et $max = 50
        //WHERE v.price BETWEEN 10 AND 50
            ->getQuery()
            ->getResult();
    }

    /*public function findLatest(int $limit = 5): array
    {
        return $this->createQueryBuilder('p') //SELECT * FROM products p
            ->orderBy('p.id', 'DESC') //ORDER BY p.id DESC
            ->setMaxResults($limit) //LIMIT 5
            ->getQuery() //transforme le query builder en objet Query Doctrine.
            ->getResult(); //exécute la requête et renvoie les entités Products correspondantes dans un tableau.
    }
*/
    //    /**
    //     * @return Products[] Returns an array of Products objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Products
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}



// Protection contre les injections SQL:
// quand on fait par ex un SELECT...FROM...WHERE... :email --> le :email est un paramètre nommé
// doctrine envoie la requete SQL au moteur sans la valeur (elle est traitée séparément), ca échappe automatiquement la valeur, ca empeche toute injection
// si on fait une requete non paramétrée, le code est injecté dans la requete et y'a risque d'injection SQL


//On écrit une requete DQL(doctrine query language) --> doctrine la comprend
// --> doctrine la traduit en SQL brut en remplacant les noms d'entités et champs
//doctrine remplace les paramètres :email par ? et garde les vraies valeurs à part
//doctrine exécute via PDO(prepare + execute) , ce qui protège contre les injections SQL
