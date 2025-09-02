<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CookieService
{
private $request; // on déclare une propriété $request qui contiendra la requete HTTP

public function __construct(RequestStack $requestStack)
{
$this->request = $requestStack->getMainRequest();
}
// Un objet RequestStack est injecté dnas le constructeur
//RequestStack permet de récuperer la requete dans un service (les services n'ont pas acces directement à $request comme les controlleurs)
//On recupere la requete avec getMainRequest()
//$this->request contient l'objet Request qui donne accès aux cookies

public function isConsentAccepted(): bool
{
return $this->request?->cookies->get('cookie') === 'accepted';
}
//on vérifie si request est null, si oui, méthode=false, si non =true


public function isConsentRejected(): bool
{
return $this->request?->cookies->get('cookie') === 'rejected';
}
}
