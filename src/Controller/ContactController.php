<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ContactType;
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

        if ($form->isSubmitted() && $form->isValid()) { // Token CSRF vérifié ici
            $data = $form->getData();

            if(!empty($form->get('website')->getData())) {
                $this->addFlash('error','Spam détecté !');
                return $this->redirectToRoute('app_contact');
            }

            $email = (new Email())
                ->from($data['email'])
                ->to('manon.sara@3wa.io') // email de l'admin
                ->subject('Nouveau message depuis le formulaire de contact')
                ->text("Nom : {$data['nom']}\nPrénom : {$data['prenom']}\nTéléphone : {$data['telephone']}\nEmail : {$data['email']}\nMessage : {$data['message']}");

            $mailer->send($email);

            $this->addFlash('success', 'Message envoyé avec succès ! Nous reviendrons vers vous au plus vite !');

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
