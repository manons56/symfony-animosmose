<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\MailerInterface;

class ContactControllerTest extends WebTestCase
{
    public function testContactPageLoads(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nous contacter');
    }

    public function testContactFormSubmission(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact');
        $form = $crawler->selectButton('Envoyer')->form();

        $formData = [
            'contact[nom]' => 'Dupont',
            'contact[prenom]' => 'Jean',
            'contact[telephone]' => '0102030405',
            'contact[email]' => 'jean.dupont@test.com',
            'contact[message]' => 'Bonjour, ceci est un test !',
            'contact[consent]' => 1,   // checkbox obligatoire
            'contact[website]' => '',  // honeypot vide
        ];

        $client->submit($form, $formData);

        // Vérifier la redirection
        $this->assertResponseRedirects('/contact');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Vérifier que le flash message est présent
        $this->assertSelectorExists('.flash-contact_success, .flash-contact_error');
    }

    public function testShopQuestionFormLoads(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/shop/question/form');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="shop"]');
    }

    public function testShopQuestionValidForm(): void
    {
        $client = static::createClient();

        $formData = [
            'shop[name]' => 'Dupont',
            'shop[phone]' => '0102030405',
            'shop[email]' => 'jean.dupont@test.com',
            'shop[message]' => 'Question sur le produit',
            'shop[website]' => '', // honeypot vide
        ];

        $client->request('POST', '/shop/question', $formData);

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testShopQuestionInvalidForm(): void
    {
        $client = static::createClient();

        // On envoie un formulaire vide pour provoquer l'erreur
        $client->request('POST', '/shop/question', []);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($data['success']);
    }
}
