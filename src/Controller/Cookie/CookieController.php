<?php

namespace App\Controller\Cookie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller to manage user cookie consent
 */
class CookieController extends AbstractController
{
    /**
     * Endpoint to save user consent for cookies (POST only)
     */
    #[Route('/cookie', name: 'app_cookie_cookie', methods: ['POST'])]
    public function setConsent(Request $request): Response
    {
        // Retrieve the user's choice from the POST request
        // 'accept' is expected to be "true" or "false" (string)
        $choice = $request->request->get('accept', 'false') === 'true';

        $response = new Response();

        // Create a cookie object
        // The cookie will store either "accepted" or "rejected" as its value
        $cookie = Cookie::create('cookie')
            ->withValue($choice ? 'accepted' : 'rejected')
            ->withExpires(strtotime('+1 day')) // expire in 1 day
            ->withPath('/')                     // available across the entire site
            ->withSecure(false)                 // false for local dev, should be true in HTTPS production
            ->withHttpOnly(false)               // allows JS access
            ->withSameSite('Lax');              // prevents CSRF on cross-site requests

        // Attach the cookie to the response headers
        $response->headers->setCookie($cookie);

        // Set the response content (can be used by JS to confirm saving)
        $response->setContent('Consent saved');

        return $response;
    }
}
