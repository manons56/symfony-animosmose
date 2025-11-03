<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;

/**
 * Controller for handling categories and their associated products.
 */
class CategoryController extends AbstractController
{
    /**
     * Home page / category index
     * Displays all main categories (parent = null) and their subcategories.
     */
    #[Route('/', name: 'home')]
    public function index(CategoriesRepository $categoryRepository)
    {
        // Retrieve all main categories (no parent) from the repository
        $categories = $categoryRepository->findBy(['parent' => null]);

        // Render the template with the categories and current page identifier
        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'current_page' => 'category',
        ]);
    }

    /**
     * Returns all products belonging to a specific category in JSON format.
     * Useful for AJAX requests or dynamic product loading.
     *
     * @param int $id The ID of the category
     * @return JsonResponse JSON response containing a list of products
     */
    #[Route('/category/{id}/products', name: 'category_products', methods: ['GET'])]
    public function getProducts($id, ProductsRepository $productRepository): JsonResponse
    {
        // Find all products with the given category ID
        $products = $productRepository->findBy(['category' => $id]);

        // Transform product entities into a simple array suitable for JSON response
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),       // Product ID
                'name' => $product->getName(),   // Product name
                'price' => $product->getPrice(), // Product price
            ];
        }

        // Return the products as a JSON response
        return new JsonResponse($data);
    }
}
