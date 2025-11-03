<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * CookieService
 * --------------------------
 * This service provides helper methods to check the user's cookie consent status.
 * It allows other parts of the application to know whether the user has accepted or rejected cookies.
 */
class CookieService
{
    private $request; // Stores the current main HTTP request

    /**
     * Constructor
     *
     * @param RequestStack $requestStack Symfony service that allows access to the current HTTP request
     *
     * Note: Services do not have direct access to the $request object like controllers,
     * so RequestStack is injected to retrieve it.
     */
    public function __construct(RequestStack $requestStack)
    {
        // Retrieves the main request from the request stack
        $this->request = $requestStack->getMainRequest();
    }

    /**
     * Check if the user has accepted cookies
     *
     * @return bool Returns true if the "cookie" cookie is set to "accepted", false otherwise
     */
    public function isConsentAccepted(): bool
    {
        // Uses nullsafe operator (?->) to safely access cookies in case $this->request is null
        return $this->request?->cookies->get('cookie') === 'accepted';
    }

    /**
     * Check if the user has rejected cookies
     *
     * @return bool Returns true if the "cookie" cookie is set to "rejected", false otherwise
     */
    public function isConsentRejected(): bool
    {
        return $this->request?->cookies->get('cookie') === 'rejected';
    }
}
