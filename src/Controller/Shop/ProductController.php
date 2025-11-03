<?php

namespace App\Controller\Shop;

use App\Entity\Products;
use App\Form\ShopType;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoriesRepository;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Controller to manage shop products: display single product, list, category filtering, menu, and new products.
 */
class ProductController extends AbstractController
{
    /**
     * Display a single product with its variants.
     * @param Products $product The product entity resolved from the route parameter.
     * @param CsrfTokenManagerInterface $csrfTokenManager CSRF token manager to secure add-to-cart actions.
     */
    #[Route('shop/product/{id}', name:'app_product_show')]
    public function show(Products $product, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        // Get all variants for this product
        $variants = $product->getVariants();

        // Prepare an array of variants formatted for JavaScript consumption
        $variantsForJs = [];
        foreach ($variants as $v) {
            $variantsForJs[] = [
                'id' => $v->getId(),  // Variant ID
                'size' => $v->getSize(), // Size option
                'color' => $v->getColor(), // Color option
                'contenance' => $v->getContenance(), // Capacity/volume if applicable
                'outOfStock' => $v->isOutOfStock(), // Boolean indicating stock availability
                'price' => $v->getPriceEuros(), // Price in Euros
                'url' => $this->generateUrl('app_shop_cart_add', ['variantId' => $v->getId()]), // URL for adding variant to cart
                'token' => $csrfTokenManager->getToken('cart_add_' . $v->getId())->getValue(), // CSRF token for secure form
            ];
        }

        // Detect if at least one variant has a color
        $hasColor = array_reduce($variantsForJs, fn($carry, $v) => $carry || !empty($v['color']), false);

        // Render product page with all prepared data
        return $this->render('shop/product/show.html.twig', [
            'product' => $product,
            'variants' => $variants,
            'variants_for_js' => $variantsForJs,
            'hasColor' => $hasColor,
            'current_page' => 'product'
        ]);
    }

    /**
     * Display the product list, possibly filtered by price range.
     */
    #[Route('/shop/products', name:'app_product_list')]
    public function list(
        ProductsRepository $productRepository,
        CategoriesRepository $categoriesRepository,
        Request $request
    ): Response {
        // Get price filters from query parameters
        $minPrice = (int) ($request->query->get('min', 0));
        $maxPrice = (int) ($request->query->get('max', 150));

        // Fetch products in the price range
        $products = $productRepository->findByPriceRange($minPrice, $maxPrice);

        // Fetch main categories for menu
        $categories = $categoriesRepository->findBy(['parent' => null]);
        $bestSellers = $productRepository->findBy(['isBestseller' => true]);

        // Prepare a popup form for shop inquiries or questions
        $form = $this->createForm(ShopType::class);

        return $this->render('shop/product/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'current_page' => 'product',
            'bestSellers' => $bestSellers,
            'form' => $form->createView(), // Pass the rendered form to the template
        ]);
    }

    /**
     * Display products belonging to a specific category, including subcategories if applicable.
     */
    #[Route('/shop/category/{id}', name:'app_product_category')]
    public function category(ProductsRepository $productRepository, CategoriesRepository $categoriesRepo, int $id): Response
    {
        $category = $categoriesRepo->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        // If category has subcategories, fetch products in all subcategories
        if ($category->getSubcategories()->count() > 0) {
            $subIds = $category->getSubcategories()->map(fn($sub) => $sub->getId())->toArray();
            $products = $productRepository->findBy(['category' => $subIds]);
        } else {
            // Otherwise, fetch products directly in this category
            $products = $productRepository->findBy(['category' => $category]);
        }

        $bestSellers = $productRepository->findBy(['isBestseller' => true]);

        return $this->render('shop/product/index.html.twig', [
            'products' => $products,
            'categories' => $categoriesRepo->findBy(['parent' => null]), // Main categories for menu
            'current_page' => 'product',
            'bestSellers' => $bestSellers,
        ]);
    }

    /**
     * Fetch main categories for menu display.
     */
    #[Route('/shop/menu', name: 'app_product_menu')]
    public function menu(CategoriesRepository $categoriesRepo): Response
    {
        $categories = $categoriesRepo->findBy(['parent' => null]);

        return $this->render('shop/product/index.html.twig', [
            'categories' => $categories,
            'current_page' => 'product',
        ]);
    }

    /**
     * Display only new products in the shop.
     */
    #[Route('/shop/new', name: 'app_product_new')]
    public function newProducts(ProductsRepository $productsRepository, CategoriesRepository $categoriesRepo): Response
    {
        $products = $productsRepository->findBy(['isNew' => true]);
        $categories = $categoriesRepo->findBy(['parent' => null]); // For menu display
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
