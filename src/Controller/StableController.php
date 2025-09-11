<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StableController extends AbstractController
{
    #[Route('/stable', name: 'app_stable')]
    public function index(): Response
    {
        return $this->render('stable/index.html.twig', [
            'current_page' => 'stable',
        ]);
    }
}
