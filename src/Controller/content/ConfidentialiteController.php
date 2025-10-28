<?php

namespace App\Controller\content;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;



final class ConfidentialiteController extends AbstractController
{
    #[Route('/content/confidentialite', name: 'app_content_confidentialite')]
    public function index(): Response
    {
        return $this->render('content/confidentialite/index.html.twig', [
            'current_page' => 'confidentialite',
        ]);
    }

   #[Route('/rgpd/demande', name: 'rgpd_request', methods: ['POST'])]
   public function demande(Request $request, MailerInterface $mailer): Response
   {
       $nom = $request->request->get('nom');
       $email = $request->request->get('email');
       $type = $request->request->get('type');
       $message = $request->request->get('message');

       // Envoi de l'e-mail
       $mail = (new Email())
           ->from('no-reply@animosmose.bzh')
           ->replyTo($email)  // Adresse de la personne qui remplit le formulaire
           ->to('manon.sara98@gmail.com')
           ->subject('Nouvelle demande RGPD')
           ->text("Nouvelle demande RGPD reçue :\n\n".
               "Nom : $nom\n".
               "Email : $email\n".
               "Type de demande : $type\n".
               "Message :\n$message"
           );

       $mailer->send($mail);

       $this->addFlash('rgpd_success', 'Votre demande RGPD a bien été envoyée. Nous vous répondrons dans un délai de 30 jours.');
       return $this->redirectToRoute('app_content_confidentialite');
   }
}
