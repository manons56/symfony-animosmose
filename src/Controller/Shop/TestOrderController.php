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

class TestOrderController extends AbstractController
{
    #[Route('/test/create-order', name: 'test_create_order')]
    public function createTestOrder(PaylineService $payline, EntityManagerInterface $em): Response
    {
        // Récupère l'utilisateur de test
        $user = $em->getRepository(User::class)->find(2);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur de test introuvable.');
            return $this->redirectToRoute('cart_show');
        }

        // Récupère son adresse principale (OneToOne : user->getAddress())
        $address = $user->getAddress();
        if (!$address) {
            $this->addFlash('error', "L'utilisateur doit avoir une adresse pour créer la commande.");
            return $this->redirectToRoute('cart_show');
        }

        // Crée la commande
        $order = new Orders();
        $order->setUser($user);
        $order->setAddressId($address);

        // Crée un article test
        $variant = $em->getRepository(Variants::class)->find(1); // variant test
        if (!$variant) {
            $this->addFlash('error', "Variant test introuvable.");
            return $this->redirectToRoute('cart_show');
        }

        $article = new Articles();
        $article->setVariantId($variant);
        $article->setPrice('1.00');  // prix en string comme défini
        $article->setQuantity(1);

        // Ajoute l'article à la commande
        $order->addArticle($article);

        // Persiste en base
        $em->persist($article);
        $em->persist($order);
        $em->flush();

        // Crée la session Payline
        $response = $payline->createPaymentSession(
            (float)$order->getTotal(),                          // Montant
            $this->generateUrl('payment_success', [], true),   // URL succès
            $this->generateUrl('payment_cancel', [], true),    // URL annulation
            $order->getReference()                              // Référence de la commande
        );

        // Redirection vers Payline
        if (!empty($response['redirectURL'])) {
            return $this->redirect($response['redirectURL']);
        }

        $this->addFlash('error', 'Impossible de créer la session de paiement.');
        return $this->redirectToRoute('app_shop_order_show', ['id' => $order->getId()]);
    }
}
