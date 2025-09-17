<?php

namespace App\Controller\content;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CgvController extends AbstractController
{
    #[Route('/content/cgv', name: 'app_content_cgv')]
    public function index(): Response
    {
        return $this->render('content/cgv/index.html.twig', [
            'current_page' => 'cgv',
        ]);
    }
}
