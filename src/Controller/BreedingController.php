<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BreedingController extends AbstractController
{
    #[Route('/breeding', name: 'app_breeding')]
    public function index(): Response
    {
        return $this->render('breeding/index.html.twig', [
            'current_page' => 'breeding'
        ]);
    }
}
