<?php

namespace App\Controller\Shop;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoriesRepository;

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
    public function list(
        ProductsRepository $productRepository,
        CategoriesRepository $categoriesRepository,
        Request $request
    ): Response {
        // Filtre par prix
        $minPrice = (int) ($request->query->get('min', 0));
        $maxPrice = (int) ($request->query->get('max', 150));

        $products = $productRepository->findByPriceRange($minPrice, $maxPrice);

        // Menu : catégories principales
        $categories = $categoriesRepository->findBy(['parent' => null]);

        return $this->render('shop/product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'current_page' => 'product',
        ]);
    }



    #[Route('/shop/category/{id}', name:'app_product_category')]
    public function category(ProductsRepository $productRepository, CategoriesRepository $categoriesRepo, int $id): Response
    {
        $category = $categoriesRepo->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        // Si c'est une catégorie principale, on récupère tous les IDs de ses sous-catégories
        if ($category->getSubcategories()->count() > 0) {
            $subIds = $category->getSubcategories()->map(fn($sub) => $sub->getId())->toArray();
            $products = $productRepository->findBy(['category' => $subIds]);
        } else {
            // Sinon c'est une sous-catégorie ou catégorie sans sous-catégorie
            $products = $productRepository->findBy(['category' => $category]);
        }

        return $this->render('shop/product/index.html.twig', [
            'products' => $products,
            'categories' => $categoriesRepo->findBy(['parent' => null]), // pour le menu
            'current_page' => 'product',
        ]);
    }


    #[Route('/shop/menu', name: 'app_product_menu')]
    public function menu(CategoriesRepository $categoriesRepo): Response
    {
        // Récupérer toutes les catégories principales (parent = null) avec leurs sous-catégories
        $categories = $categoriesRepo->findBy(['parent' => null]);

        return $this->render('shop/product/index.html.twig', [
            'categories' => $categories,
            'current_page' => 'product',
        ]);
    }


}








