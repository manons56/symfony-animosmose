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


final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request); // symfony vérifie le token pour POST
        // on appelle la méthode handleRequest($request), symfony récupère la valeur du champ _token dans le POST
        //puis compare cette valeur avec le token qu'il a généré en interne(via le servie CsrfTokenManagerInterface)
        //si le token ne correspond pas, le formulaire est invalide


        if ($form->isSubmitted()) {
            // Honeypot
            if (!empty($form->get('website')->getData())) {
                $this->addFlash('contact_error', 'Spam détecté !');
            }

            // Formulaire valide
            if ($form->isValid() /* Token CSRF vérifié ici*/ && empty($form->get('website')->getData())) {
                $data = $form->getData();

                $email = (new Email())
                    ->from('manon.sara@3wa.io')
                    ->to('manon.sara96@gmail.com') // email de l'admin
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

        //Rendu du formulaire, toujours exécuté même si formulaire pas soumis ou invalide
        return $this->render('contact/index.html.twig', [
            'current_page' => 'contact',
            'contactForm' => $form->createView(),
        ]);
    }




    #[Route('/shop/question', name: 'shop_question', methods: ['POST'])]
    public function send(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ShopType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new Email())
                ->from($data['email'])
                ->to('manon.sara96@gmail.com') // email de l'admin
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

    //shop_question → POST → reçoit les données du formulaire et envoie l’email.
    //shop_question_form → GET → sert juste à rendre le formulaire HTML, pour que la popup puisse le charger via AJAX après 5 secondes.


    #[Route('/shop/question/form', name: 'shop_question_form', methods: ['GET'])]
    public function questionForm(): Response
    {
        $form = $this->createForm(ShopType::class);

        return $this->render('shop/order/_question_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
