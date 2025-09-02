<?php

namespace App\Controller\content;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FaqController extends AbstractController
{
    #[Route('/content/faq', name: 'app_content_faq')]
    public function index(): Response
    {
        return $this->render('content/faq/index.html.twig', [
            'controller_name' => 'FaqController',
        ]);
    }
}
