<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Si l'utilisateur est déjà connecté, on le redirige selon son rôle
        if ($this->getUser()) {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('admin_dashboard');
            }
            return $this->redirectToRoute('app_shop_order'); // page utilisateur classique
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, // le terme username est défini par symfony, c'est dans le yaml ensuite qu'on lui dit que le username, c'est pour ce projet, l'email dont on se sert pour la connexion
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }




}
