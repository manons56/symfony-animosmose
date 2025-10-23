<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\Address;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('', name: 'app_user_user')]
    public function index(): Response
    {
        return $this->render('user/user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/new', name: 'app_user_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager,  UserPasswordHasherInterface $userPasswordHasher): Response
    {

        $user = new User();
        $address = new Address();


        $user->setAddress($address); // pour avoir le formulaire Address imbriqué dans le formulaire de User

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $address->setUserId($user);
            $user->setAddress($address);

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $manager->persist($user);
            $manager->persist($address); // on utilise l'adresse dans User donc il faut enregistrer l'adresse aussi dans la BDD depuis le UserController
            $manager->flush();

            return $this->redirectToRoute('app_shop_cart_checkout_trigger');
            // redirection après création
            //1	Clique "Valider panier"	checkout_trigger
            //2	Pas connecté → login	/login
            //3	Clique "Créer un compte"	/user/user/new
            //4	Inscription OK → redirection vers	app_shop_cart_checkout_trigger
            //5	Checkout POST	app_shop_cart_checkout
            //6	Création commande	app_shop_order_show/{id}

        }


        return $this->render('user/user/new.html.twig', [
            'form' => $form,
            'current_page' => 'new'
        ]);


    }

    #[Route('/edit', name: 'app_user_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true, // on passe en mode édition
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash('success', 'Vos informations ont bien été mises à jour.');
            return $this->redirectToRoute('app_user_user_edit');
        }

        return $this->render('user/user/edit.html.twig', [
            'form' => $form,
            'current_page' => 'edit',
        ]);
    }




}
