<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class InterviewController extends AbstractController
{
    #[Route('/interview', name: 'app_interview')]
    public function index(): Response
    {
        return $this->render('interview/index.html.twig', [
            'current_page' => 'interview'
        ]);
    }
}
