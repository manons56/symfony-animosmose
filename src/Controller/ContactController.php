<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ContactType;
use App\Form\ShopType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * ContactController
 * --------------------------
 * Handles contact form submissions and shop-related questions.
 * Key responsibilities:
 * 1. Render and process the general contact form
 * 2. Handle shop-specific question forms (AJAX)
 * 3. Send emails to the admin with form data
 * --------------------------
 */
final class ContactController extends AbstractController
{
    /**
     * Render and process the general contact form.
     * URL: /contact
     */
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        // handleRequest checks CSRF token automatically and validates POST data

        if ($form->isSubmitted()) {
            // Honeypot anti-spam check
            if (!empty($form->get('website')->getData())) {
                $this->addFlash('contact_error', 'Spam détecté !');
            }

            // Form is valid and honeypot is empty → send email
            if ($form->isValid() && empty($form->get('website')->getData())) {
                $data = $form->getData();

                $email = (new Email())
                    ->from('manon.sara@3wa.io')
                    ->to('manon.sara96@gmail.com') // admin email
                    ->replyTo($data['email'])
                    ->subject('Nouveau message depuis le formulaire de contact')
                    ->text(
                        "Nom : {$data['nom']}\n" .
                        "Prénom : {$data['prenom']}\n" .
                        "Téléphone : {$data['telephone']}\n" .
                        "Email : {$data['email']}\n" .
                        "Message : {$data['message']}"
                    );

                try {
                    $mailer->send($email);
                    $this->addFlash('contact_success', 'Message envoyé avec succès ! Nous reviendrons vers vous dès que possible.');
                } catch (\Throwable $e) {
                    $this->addFlash('contact_error', 'Erreur lors de l\'envoi du mail : ' . $e->getMessage());
                }

                return $this->redirectToRoute('app_contact');
            }
        }

        // Render the form (even if not submitted or invalid)
        return $this->render('contact/index.html.twig', [
            'current_page' => 'contact',
            'contactForm' => $form->createView(),
        ]);
    }

    /**
     * Handle shop-related question form submissions via AJAX (POST)
     * URL: /shop/question
     */
    #[Route('/shop/question', name: 'shop_question', methods: ['POST'])]
    public function send(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ShopType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new Email())
                ->from($data['email'])
                ->to('manon.sara96@gmail.com') // admin email
                ->replyTo($data['email'])
                ->subject('Nouveau message depuis la boutique')
                ->text(sprintf(
                    "Nom: %s\nTéléphone: %s\nEmail: %s\nMessage:\n%s",
                    $data['name'],
                    $data['phone'],
                    $data['email'],
                    $data['message']
                ));

            $mailer->send($email);

            return $this->json(['success' => true]);
        }

        return $this->json(['success' => false], 400);
    }

    /**
     * Render the shop question form (GET)
     * Used to load the form via AJAX in a popup
     * URL: /shop/question/form
     */
    #[Route('/shop/question/form', name: 'shop_question_form', methods: ['GET'])]
    public function questionForm(): Response
    {
        $form = $this->createForm(ShopType::class);

        return $this->render('shop/order/_question_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
