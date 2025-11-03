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

/**
 * Controller managing user registration, edition, and profile.
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * Display a user dashboard or homepage (empty in this case).
     */
    #[Route('', name: 'app_user_user')]
    public function index(): Response
    {
        return $this->render('user/user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * Handle creation of a new user (registration form)
     */
    #[Route('/new', name: 'app_user_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();          // create new user entity
        $address = new Address();    // create new address entity

        $user->setAddress($address); // associate address to user

        // Create the registration form
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request); // handle submission

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the user for the address (foreign key)
            $address->setUserId($user);
            $user->setAddress($address);

            // Hash the plain password and set it on the user
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Persist user and address entities to the database
            $manager->persist($user);
            $manager->persist($address);
            $manager->flush();

            // Redirect after successful registration
            // Flow:
            // 1. Click "Valider panier" → checkout_trigger
            // 2. If not logged in → /login
            // 3. Click "Créer un compte" → /user/user/new
            // 4. Registration OK → redirect to app_shop_cart_checkout_trigger
            // 5. Checkout POST → app_shop_cart_checkout
            // 6. Order created → app_shop_order_show/{id}
            return $this->redirectToRoute('app_shop_cart_checkout_trigger');
        }

        // Render the registration form
        return $this->render('user/user/new.html.twig', [
            'form' => $form,
            'current_page' => 'new'
        ]);
    }

    /**
     * Handle editing of user profile
     */
    #[Route('/edit', name: 'app_user_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser(); // fetch currently logged-in user

        // Redirect to login if no user is authenticated
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Create the user edit form with 'is_edit' option
        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true, // mark the form as editing mode
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush(); // save changes to database

            // Flash message to confirm update
            $this->addFlash('success', 'Vos informations ont bien été mises à jour.');

            // Redirect to the same edit page after update
            return $this->redirectToRoute('app_user_user_edit');
        }

        // Render the edit form
        return $this->render('user/user/edit.html.twig', [
            'form' => $form,
            'current_page' => 'edit',
        ]);
    }
}
