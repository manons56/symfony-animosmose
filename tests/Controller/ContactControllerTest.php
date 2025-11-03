<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

        // Récupérer la page du formulaire de contact
        $crawler = $client->request('GET', '/contact');
        $this->assertResponseIsSuccessful();

        // Récupérer le formulaire via le bouton "Envoyer"
        $form = $crawler->selectButton('Envoyer')->form();

        // Remplir les champs
        $form['contact[nom]'] = 'Dupont';
        $form['contact[prenom]'] = 'Jean';
        $form['contact[telephone]'] = '0102030405';
        $form['contact[email]'] = 'jean.dupont@test.com';
        $form['contact[message]'] = 'Bonjour, ceci est un test !';
        $form['contact[consent]'] = 1; // checkbox RGPD
        $form['contact[website]'] = ''; // honeypot vide

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier la redirection
        $this->assertResponseRedirects('/contact');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Vérifier qu’au moins un flash message est présent
        $this->assertSelectorExists('.flash-success, .flash-danger');
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

        // Récupérer le formulaire via la route GET
        $crawler = $client->request('GET', '/shop/question/form');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Envoyer')->form();

        // Remplir les champs du formulaire
        $form['shop[name]'] = 'Jean Dupont';
        $form['shop[phone]'] = '0601020304';
        $form['shop[email]'] = 'client@example.com';
        $form['shop[message]'] = 'Bonjour, ceci est un test depuis PHPUnit.';

        // Soumettre le formulaire → POST automatique vers /shop/question
        $client->submit($form);

        // Vérifier la réponse JSON
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testShopQuestionInvalidForm(): void
    {
        $client = static::createClient();

        // On envoie un formulaire vide directement en POST
        $client->request('POST', '/shop/question', []);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($data['success']);
    }
}
