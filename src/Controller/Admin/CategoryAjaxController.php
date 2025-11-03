<?php

namespace App\Controller\Admin;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller to handle AJAX requests for categories.
 * It provides subcategories for a given parent category in JSON format,
 * which can be used in dynamic dropdowns or other frontend features in the admin panel.
 */
class CategoryAjaxController extends AbstractController
{
    #[Route('/admin/subcategories/{parentId}', name: 'admin_subcategories', methods: ['GET'])]
    public function getSubcategories(int $parentId, CategoriesRepository $repo): JsonResponse
    {
        // Fetch subcategories where 'parent' matches the given parent ID
        $subs = $repo->findBy(['parent' => $parentId]);

        // Transform the result into a simple array containing only id and name
        $data = array_map(fn($c) => ['id' => $c->getId(), 'name' => $c->getName()], $subs);

        // Return the data as a JSON response
        return $this->json($data);
    }
}
