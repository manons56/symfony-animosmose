<?php

namespace App\Controller\Shop;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoriesRepository;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


class ProductController extends AbstractController
{
    #[Route('shop/product/{id}', name:'app_product_show')]
    public function show(Products $product, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $variants = $product->getVariants();

        // Préparer un tableau formaté pour le JS
        $variantsForJs = [];
        foreach ($product->getVariants() as $v) {
            $variantsForJs[] = [
                'id' => $v->getId(),
                'size' => $v->getSize(),
                'color' => $v->getColor(),
                'contenance' => $v->getContenance(),
                'outOfStock' => $v->isOutOfStock(),
                'price' => $v->getPriceEuros(),
                'url' => $this->generateUrl('app_shop_cart_add', ['variantId' => $v->getId()]),
                'token' => $csrfTokenManager->getToken('cart_add_' . $v->getId())->getValue(), // ✅ ICI !
            ];
        }

        // Détection d'au moins une couleur
        $hasColor = array_reduce($variantsForJs, fn($carry, $v) => $carry || !empty($v['color']), false);

        return $this->render('shop/product/show.html.twig', [
            'product' => $product,
            'variants' => $product->getVariants(),
            'variants_for_js' => $variantsForJs,
            'hasColor' => $hasColor,
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
        $bestSellers = $productRepository->findBy(['isBestseller' => true]);

        return $this->render('shop/product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'current_page' => 'product',
            'bestSellers' => $bestSellers,
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

        $bestSellers = $productRepository->findBy(['isBestseller' => true]);


        return $this->render('shop/product/index.html.twig', [
            'products' => $products,
            'categories' => $categoriesRepo->findBy(['parent' => null]), // pour le menu
            'current_page' => 'product',
            'bestSellers' => $bestSellers,
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


    #[Route('/shop/new', name: 'app_product_new')]
    public function newProducts(ProductsRepository $productsRepository, CategoriesRepository $categoriesRepo): Response
    {
        $products = $productsRepository->findBy(['isNew' => true]);
        $categories = $categoriesRepo->findBy(['parent' => null]); // pour le menu
        $bestSellers = $productsRepository->findBy(['isBestseller' => true]);

        return $this->render('shop/product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'title' => 'Nouveaux produits',
            'current_page' => 'product',
            'bestSellers' => $bestSellers,
        ]);
    }


}








