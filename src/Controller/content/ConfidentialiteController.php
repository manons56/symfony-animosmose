<?php

namespace App\Controller\content;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Controller to handle privacy-related pages and GDPR requests
 */
final class ConfidentialiteController extends AbstractController
{
    /**
     * Display the privacy/confidentialité page
     */
    #[Route('/content/confidentialite', name: 'app_content_confidentialite')]
    public function index(): Response
    {
        // Render the Twig template and pass the current page identifier
        return $this->render('content/confidentialite/index.html.twig', [
            'current_page' => 'confidentialite',
        ]);
    }

    /**
     * Handle GDPR request form submission (POST only)
     */
    #[Route('/rgpd/demande', name: 'rgpd_request', methods: ['POST'])]
    public function demande(Request $request, MailerInterface $mailer): Response
    {
        // Retrieve form data from the POST request
        $nom = $request->request->get('nom');
        $email = $request->request->get('email');
        $type = $request->request->get('type');
        $message = $request->request->get('message');

        // Send an email to the admin with the GDPR request details
        $mail = (new Email())
            ->from('no-reply@animosmose.bzh') // system email
            ->replyTo($email)                  // reply-to set to the user who submitted the form
            ->to('manon.sara98@gmail.com')     // recipient email (admin)
            ->subject('Nouvelle demande RGPD') // email subject in French
            ->text(
                "Nouvelle demande RGPD reçue :\n\n".
                "Nom : $nom\n".
                "Email : $email\n".
                "Type de demande : $type\n".
                "Message :\n$message"
            );

        $mailer->send($mail);

        // Flash message in French confirming the request was sent
        $this->addFlash('rgpd_success', 'Votre demande RGPD a bien été envoyée. Nous vous répondrons dans un délai de 30 jours.');

        // Redirect back to the privacy page
        return $this->redirectToRoute('app_content_confidentialite');
    }
}
