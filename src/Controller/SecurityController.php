<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * SecurityController
 * --------------------------
 * This controller manages user authentication: login and logout.
 * It handles displaying the login form, managing login errors, and redirecting users after login.
 */
class SecurityController extends AbstractController
{
    /**
     * Login route
     *
     * @param AuthenticationUtils $authenticationUtils Provides methods to retrieve login errors and last entered username
     *
     * @return Response Renders the login form or redirects authenticated users
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Retrieve the last authentication error (if any)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Retrieve the last username entered by the user (used to pre-fill the form)
        $lastUsername = $authenticationUtils->getLastUsername();

        // If the user is already logged in, redirect them based on their role
        if ($this->getUser()) {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                // Redirect admin users to the admin dashboard
                return $this->redirectToRoute('admin_dashboard');
            }
            // Redirect regular users to the order page
            return $this->redirectToRoute('app_shop_order');
        }

        // Render the login template with the last username and any error message
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, // Last username input
            'error' => $error,                // Any login error message
            'current_page' => 'login',        // Useful for template highlighting/navigation
        ]);
    }

    /**
     * Logout route
     *
     * Note: This method is never executed directly. The Symfony firewall intercepts the route
     * and handles the logout process automatically.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
