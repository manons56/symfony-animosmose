<?php
namespace App\Controller\Shop;

use App\Service\PaylineService;
use App\Entity\Orders;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\OrderStatus;

class PaymentController extends AbstractController
{
    /**
     * Route appelée quand Payline renvoie un succès ou une fin de session de paiement
     */
    #[Route('/payment/success', name: 'payment_success')]
    public function success(Request $request, PaylineService $payline, EntityManagerInterface $em): Response
    {
        // On récupère le token Payline envoyé en paramètre
        $token = $request->query->get('token');
        if (!$token) {
            // Si pas de token → erreur et retour à la liste des produits
            $this->addFlash('error', 'Token manquant.');
            return $this->redirectToRoute('app_product_list');
        }

        // On récupère les détails de la transaction depuis Payline
        $details = $payline->getPaymentDetails($token);
        $code = $details['result']['code'] ?? null;    // Code retour Payline
        $orderRef = $details['order']['ref'] ?? null; // Référence commande envoyée lors de la création

        /** @var Orders|null $order */
        $order = $em->getRepository(Orders::class)->findOneBy([
            'reference' => $orderRef,
            'status' => OrderStatus::Pending // On vérifie que la commande est encore "en attente"
        ]);

        if (!$order) {
            // Si commande introuvable ou déjà traitée → on prévient l’utilisateur
            $this->addFlash('warning', 'Commande introuvable ou déjà traitée.');
            return $this->redirectToRoute('app_product_list');
        }

        // On gère les différents codes retour de Payline
        switch ($code) {
            // --- Paiement accepté ---
            case '00000': // succès standard
            case '02500': // succès avec 3D Secure
                $order->setStatus(OrderStatus::Paid);
                $this->addFlash('success', 'Paiement accepté. Merci pour votre commande !');
                break;

            // --- Annulation utilisateur ---
            case '02008':
                $order->setStatus(OrderStatus::Canceled);
                $this->addFlash('warning', 'Vous avez annulé le paiement.');
                break;

            // --- Paiement refusé ---
            case '02020': // refus émetteur
            case '02021': // fraude détectée
            case '02324': // session expirée
                $order->setStatus(OrderStatus::Failed);
                $this->addFlash('error', 'Paiement refusé : ' . ($details['result']['shortMessage'] ?? 'Transaction refusée'));
                break;

            // --- Erreurs techniques ---
            case '02101': // erreur interne Payline
            case '02102': // problème de communication avec l’acquéreur
            case '02305': // champ invalide
            case '02308': // valeur invalide
                $order->setStatus(OrderStatus::Failed);
                $this->addFlash('error', 'Erreur technique lors du paiement.');
                break;

            // --- Cas par défaut : tout autre code non prévu ---
            default:
                $order->setStatus(OrderStatus::Failed);
                $this->addFlash('error', 'Paiement refusé : ' . ($details['result']['shortMessage'] ?? 'Erreur inconnue'));
        }

        // On sauvegarde l’état de la commande
        $em->flush();

        // Redirection vers la page de la commande
        return $this->redirectToRoute('app_shop_order_show', ['id' => $order->getId()]);
    }

    /**
     * Route appelée si l’utilisateur annule directement le paiement
     */
    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        // Message flash d’avertissement
        $this->addFlash('warning', 'Vous avez annulé le paiement.');

        // Retour à la liste des produits
        return $this->redirectToRoute('app_product_list');
    }

    /**
     * Démarre le processus de paiement pour une commande donnée
     */
    #[Route('/checkout/pay/{orderId}', name: 'checkout_pay')]
    public function pay(int $orderId, PaylineService $payline, EntityManagerInterface $em): Response
    {
        /** @var Orders|null $order */
        $order = $em->getRepository(Orders::class)->find($orderId);

        if (!$order) {
            // Si la commande n’existe pas → erreur
            $this->addFlash('error', 'Commande introuvable.');
            return $this->redirectToRoute('app_product_list');
        }

        // Création d’une session de paiement auprès de Payline
        $response = $payline->createPaymentSession(
            (float)$order->getTotal(),                               // Montant de la commande
            $this->generateUrl('payment_success', [], true),        // URL de retour si succès
            $this->generateUrl('payment_cancel', [], true),         // URL de retour si annulation
            $order->getReference()                                  // Référence unique de la commande
        );

        // Si Payline fournit une URL de redirection → on y envoie le client
        if (!empty($response['redirectURL'])) {
            return $this->redirect($response['redirectURL']);
        }

        // Sinon, erreur → retour sur la commande
        $this->addFlash('error', 'Impossible de créer la session de paiement.');
        return $this->redirectToRoute('app_shop_order_show', ['id' => $order->getId()]);
    }
}
