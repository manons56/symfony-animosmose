<?php

namespace App\Controller\Shop;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    #[Route('shop/product/{id}', name:'app_product_show')]
    public function show(Products $product): Response
{
    return $this->render('shop/product/show.html.twig', [
        'product' => $product,
        'variants' => $product->getVariants(),
    ]);
}

    #[Route('/shop/products', name:'app_product_list')]
    public function list(ProductsRepository $productRepository, Request $request): Response
    {
        // Récupérer les valeurs min et max depuis l'URL
        $minPrice = $request->query->get('min');
        $maxPrice = $request->query->get('max');

        // On commence par créer le QueryBuilder pour les produits
        $query = $productRepository->createQueryBuilder('p');

        // Si l'utilisateur a défini un prix minimum
        //  /produits?min=50 → SELECT * FROM products WHERE price >= 50.
        if ($minPrice !== null && $minPrice !== '') {
            $query->andWhere('p.price >= :min')
                ->setParameter('min', $minPrice);
        }

        // Si l'utilisateur a défini un prix maximum
        // /produits?max=100 → SELECT * FROM products WHERE price <= 100.
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->andWhere('p.price <= :max')
                ->setParameter('max', $maxPrice);
        }


        //  /produits?min=50&max=100 → SELECT * FROM products WHERE price >= 50 AND price <= 100.
        // Si min et max sont définis → on récupère p.price BETWEEN min AND max.
        //Si les deux valeurs sont définies, les deux conditions andWhere sont ajoutées.
        //Doctrine les combine automatiquement avec AND → équivalent à SQL :
        //SELECT * FROM products p WHERE p.price >= :min AND p.price <= :max
        // La logique “BETWEEN min AND max” n’existe pas littéralement, mais elle est construite dynamiquement grâce aux deux andWhere combinés dans le QueryBuilder.


        // /produits → SELECT * FROM products → tous les produits, si les deux if ne sont pas vérifiés


        // On exécute la requête pour récupérer les produits filtrés
        $products = $query->getQuery()->getResult();

        // Rendu du template avec les produits filtrés
        return $this->render('shop/product/index.html.twig', [
            'products' => $products,
        ]);
    }

}








