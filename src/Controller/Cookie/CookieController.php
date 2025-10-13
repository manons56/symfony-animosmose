<?php

namespace App\Controller\Cookie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CookieController extends AbstractController
{
    #[Route('/cookie', name: 'app_cookie_cookie', methods: ['POST'])]
    public function setConsent(Request $request): Response
    {
        $choice = $request->request->get('accept', 'false') === 'true';
        $response = new Response();

        // Important : le chemin "/" rend le cookie accessible sur tout le site
        $cookie = Cookie::create('cookie')
            ->withValue($choice ? 'accepted' : 'rejected')
            ->withExpires(strtotime('+1 day'))
            ->withPath('/') // permet que toutes les pages y aient accès
            ->withSecure(false) // mets false en local sinon il ne s’enregistre pas (true en prod HTTPS)
            ->withHttpOnly(false)
            ->withSameSite('Lax');

        $response->headers->setCookie($cookie);
        $response->setContent('Consent saved');

        return $response;
    }
}
