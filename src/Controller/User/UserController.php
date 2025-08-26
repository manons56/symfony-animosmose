<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/user')]
final class UserController extends AbstractController
{
    #[Route('', name: 'app_user_user')]
    public function index(): Response
    {
        return $this->render('user/user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/new', name: 'app_user_user_new', methods: ['GET'])]
    public function new(): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        return $this->render('user/user/new.html.twig', [
            'form' => $form,
        ]);
    }
}
