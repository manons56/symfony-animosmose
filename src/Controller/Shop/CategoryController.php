<?php

namespace App\Controller\Shop;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;

class CategoryController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategoriesRepository $categoryRepository)
    {
        // Récupère toutes les catégories et sous-catégories
        $categories = $categoryRepository->findBy(['parent' => null]);

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
            'current_page' => 'category',
        ]);
    }

    #[Route('/category/{id}/products', name: 'category_products', methods: ['GET'])]
    public function getProducts($id, ProductsRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findBy(['category' => $id]);

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
            ];
        }

        return new JsonResponse($data);
    }
}
