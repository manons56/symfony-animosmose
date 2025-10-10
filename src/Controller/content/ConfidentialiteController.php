<?php

namespace App\Controller\content;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConfidentialiteController extends AbstractController
{
    #[Route('/content/confidentialite', name: 'app_content_confidentialite')]
    public function index(): Response
    {
        return $this->render('content/confidentialite/index.html.twig', [
            'current_page' => 'confidentialite',
        ]);
    }
}
