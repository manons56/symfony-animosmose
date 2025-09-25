<?php

namespace App\Controller\content;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LegalNoticesController extends AbstractController
{
    #[Route('/content/legalnotices', name: 'app_content_legalnotices')]
    public function index(): Response
    {
        return $this->render('content/legalnotices/index.html.twig', [
            'current_page' => 'legalnotices',
        ]);
    }
}
