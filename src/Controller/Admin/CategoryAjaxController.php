<?php

namespace App\Controller\Admin;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CategoryAjaxController extends AbstractController
{
    #[Route('/admin/subcategories/{parentId}', name: 'admin_subcategories', methods: ['GET'])]
    public function getSubcategories(int $parentId, CategoriesRepository $repo): JsonResponse
    {
        $subs = $repo->findBy(['parent' => $parentId]);
        $data = array_map(fn($c) => ['id' => $c->getId(), 'name' => $c->getName()], $subs);
        return $this->json($data);
    }
}
