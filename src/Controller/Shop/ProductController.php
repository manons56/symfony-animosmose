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
        'current_page' => 'product'
    ]);
}

    #[Route('/shop/products', name:'app_product_list')]
    public function list(ProductsRepository $productRepository, Request $request): Response
    {
        // Récupérer les valeurs min et max depuis le formulaire
        $minPrice = (int) ($request->query->get('min', 0));

        $maxPrice = (int) ($request->query->get('max', 150));



        // Récupérer les produits filtrés par le repository
        $products = $productRepository->findByPriceRange($minPrice, $maxPrice);


        // Rendu du template avec les produits filtrés
        return $this->render('shop/product/index.html.twig', [
            'products' => $products,
            'current_page' => 'product',
        ]);


    }

}








