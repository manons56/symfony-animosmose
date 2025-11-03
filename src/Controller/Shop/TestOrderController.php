<?php

namespace App\Controller\Shop;

use App\Entity\Orders;
use App\Entity\Articles;
use App\Entity\User;
use App\Entity\Variants;
use App\Service\PaylineService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller to create a test order and initiate a payment session with Payline.
 * This is primarily for testing purposes.
 */
class TestOrderController extends AbstractController
{
    /**
     * Route to create a test order and redirect to Payline payment.
     */
    #[Route('/test/create-order', name: 'test_create_order')]
    public function createTestOrder(PaylineService $payline, EntityManagerInterface $em): Response
    {
        // Fetch a test user (user with ID 2)
        $user = $em->getRepository(User::class)->find(2);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur de test introuvable.');
            return $this->redirectToRoute('cart_show');
        }

        // Get the user's main address (OneToOne relation: User->Address)
        $address = $user->getAddress();
        if (!$address) {
            $this->addFlash('error', "L'utilisateur doit avoir une adresse pour créer la commande.");
            return $this->redirectToRoute('cart_show');
        }

        // Create a new order and associate it with the user and address
        $order = new Orders();
        $order->setUser($user);
        $order->setAddressId($address);

        // Create a test article
        $variant = $em->getRepository(Variants::class)->find(1); // test variant
        if (!$variant) {
            $this->addFlash('error', "Variant test introuvable.");
            return $this->redirectToRoute('cart_show');
        }

        $article = new Articles();
        $article->setVariantId($variant);
        $article->setPrice('1.00');  // price stored as string
        $article->setQuantity(1);

        // Add the article to the order
        $order->addArticle($article);

        // Persist both the article and the order to the database
        $em->persist($article);
        $em->persist($order);
        $em->flush();

        // Create a Payline payment session for this order
        $response = $payline->createPaymentSession(
            (float)$order->getTotal(),                          // Payment amount
            $this->generateUrl('payment_success', [], true),   // Success URL
            $this->generateUrl('payment_cancel', [], true),    // Cancel URL
            $order->getReference()                              // Order reference
        );

        // Redirect user to Payline if a redirect URL is provided
        if (!empty($response['redirectURL'])) {
            return $this->redirect($response['redirectURL']);
        }

        // Flash message in case the payment session creation fails
        $this->addFlash('error', 'Impossible de créer la session de paiement.');
        return $this->redirectToRoute('app_shop_order_show', ['id' => $order->getId()]);
    }
}
