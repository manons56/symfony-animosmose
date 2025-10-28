<?php

namespace App\Controller\Shop;

use OnlinePayments\Sdk\Communicator;
use OnlinePayments\Sdk\CommunicatorConfiguration;
use OnlinePayments\Sdk\Authentication\V1HmacAuthenticator;
use OnlinePayments\Sdk\Merchant\MerchantClientFactory;
use OnlinePayments\Sdk\Domain\CreatePaymentRequest;
use OnlinePayments\Sdk\Domain\Order;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\OrderReferences;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment_test')]
    public function index(): Response
    {
        $apiKeyId = '8443169C6FFCDC0DCC17';
        $apiSecret = '3341411052400EE99D4939F7276FCB9CF3225DF1CEFC13E31311531C93D9A6D2E23C0F98F37C704988E77E51AC8CE7717B03E8F7DA562D92B1D362FB7412E8DC';
        $apiEndpoint = 'https://payment-api.sandbox.online-payments.com';
        $integrator = 'Symfony-App/1.0';
        $merchantId = 'YOUR_MERCHANT_ID';

        try {
            //  Authenticator
            $authenticator = new V1HmacAuthenticator(
                new CommunicatorConfiguration($apiKeyId, $apiSecret, $apiEndpoint, $integrator)
            );

            //  Communicator
            $communicatorConfig = new CommunicatorConfiguration($apiKeyId, $apiSecret, $apiEndpoint, $integrator);
            $communicator = new Communicator($communicatorConfig, $authenticator);

            //  MerchantClient via factory
            $factory = new MerchantClientFactory();
            $merchantClient = $factory->createClient($communicator, $merchantId);

            //  Créer un CreatePaymentRequest
            $paymentRequest = new CreatePaymentRequest();

            //  Définir l'ordre
            $order = new Order();
            $amount = new AmountOfMoney();
            $amount->setAmount(1000); // 10 € en centimes
            $amount->setCurrencyCode('EUR');
            $order->setAmountOfMoney($amount);

            //  Définir la référence
            $references = new OrderReferences();
            $references->setMerchantReference('commande123');
            $order->setReferences($references);

            $paymentRequest->setOrder($order);

            //  Créer le paiement via le SDK
            $paymentResponse = $merchantClient->payments()->createPayment($paymentRequest);

            return new Response('Transaction créée ! ID : ' . $paymentResponse->getId());

        } catch (\Exception $e) {
            return new Response('Erreur : ' . $e->getMessage());
        }
    }
}

