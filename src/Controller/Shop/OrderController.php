<?php

namespace App\Controller\Shop;

use App\Entity\Orders;
use App\Enum\OrderStatus;
use App\Form\DeliveryType;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\CartService; // importe le CartService

final class OrderController extends AbstractController
{
    #[Route('/shop/order', name: 'app_shop_order')]
    #[IsGranted('ROLE_USER')]
    public function index(OrdersRepository $ordersRepository): Response
    {
        $user = $this->getUser();
        // On récupère uniquement les commandes de l’utilisateur connecté
        $orders = $ordersRepository->findCompletedOrdersForUser($user);

        return $this->render('shop/order/index.html.twig', [
            'orders' => $orders,
            'current_page' => 'order',
        ]);
    }

    #[Route('/shop/order/{id}', name: 'app_shop_order_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Request $request, Orders $order, EntityManagerInterface $em): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à cette commande.');
        }

        // --- Définition des options de livraison avec leurs prix ---
        $deliveryOptions = [
            'relay' => ['label' => 'Point relais', 'price' => 8.00, 'description' => 'Retrait dans un point relais proche de chez vous avec MondialRelay.'],
            'home' => ['label' => 'Livraison à domicile', 'price' => 5.00, 'description' => 'Livraison directement à votre domicile, 1 fois par mois. Livraison possible dans le 56, 35 et 44.'],
            'pickup' => ['label' => 'Retrait sur place', 'price' => 0.00, 'description' => 'Vous venez récupérer votre commande en magasin, gratuitement.'],
        ];

        $form = $this->createForm(DeliveryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selectedDelivery = $data['delivery_method'];


                $order->setDeliveryMethod($selectedDelivery);
                $order->setStatus(OrderStatus::Pending);
                $em->flush();


                // Redirection  pour home et pickup
                if ($selectedDelivery === 'home') {
                    return $this->redirectToRoute('app_shop_homedelivery');
                }

                if ($selectedDelivery === 'pickup') {
                    return $this->redirectToRoute('app_shop_pickup');
                }


            // Si mode 'relay', on redirige vers Payline pour paiement en ligne
            if ($selectedDelivery === 'relay') {
                // On stocke temporairement la sélection de livraison en session
                $request->getSession()->set('relay_pending_order', [
                    'order_id' => $order->getId(),
                ]);


                return $this->redirectToRoute('checkout_pay', ['orderId' => $order->getId()]);
            }
        }


        return $this->render('shop/order/show.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
            'current_page' => 'order',
            'deliveryOptions' => $deliveryOptions,
        ]);
    }

    #[Route('/shop/order/{id}/delete', name: 'app_shop_order_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Orders $order, EntityManagerInterface $em): Response
    {
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        if ($this->isCsrfTokenValid('delete_order_' . $order->getId(), $request->request->get('_token'))) {
            if ($order->getStatus() === OrderStatus::Pending) {
                $em->remove($order);
                $em->flush();
                $this->addFlash('success', 'Commande supprimée.');
            } else {
                $this->addFlash('warning', 'Seules les commandes en attente peuvent être supprimées.');
            }
        }

        return $this->redirectToRoute('app_shop_order');
    }

    #[Route('/api/mondial-relay/save', name: 'api_mondial_relay_save', methods: ['POST'])] // Déclare une route Symfony accessible via POST à l'URL '/api/mondial-relay/save' avec le nom 'api_mondial_relay_save'
    public function savePointRelay(Request $request, EntityManagerInterface $em): JsonResponse // Méthode qui sera exécutée pour cette route, reçoit la requête HTTP et l'Entity Manager pour interagir avec la DB, retourne un JSON
    {
        $data = json_decode($request->getContent(), true); // Récupère le contenu JSON de la requête et le transforme en tableau PHP associatif

        $order = $em->getRepository(Orders::class)->findOneBy([ // Cherche une commande dans la base
            'user' => $this->getUser(),                          // qui appartient à l'utilisateur connecté
            'status' => OrderStatus::Pending                     // et dont le statut est "Pending" (en attente)
        ]);

        if ($order) {                                           // Si une commande correspondante existe
            $order->setDeliveryMethod('relay');                // On définit le mode de livraison sur "relay" (point relais)
            // Tu peux ajouter un champ dans ton entité Orders pour stocker les infos du relais :
            // ex : $order->setRelayInfo(json_encode($data));  // Exemple : stocker les infos du relais en JSON dans la commande
            $em->flush();                                      // Sauvegarde les modifications en base de données
        }

        return $this->json(['success' => true]);               // Renvoie une réponse JSON indiquant que l'opération a réussi
    }


    #[Route('/shop/homedelivery', name: 'app_shop_homedelivery')]
    public function homeDelivery(CartService $cartService): Response
    {
        // On vide le panier seulement maintenant
        $cartService->clear();
        return $this->render('shop/order/homedelivery.html.twig', [
            'current_page' => 'homedelivery',
        ]);
    }

    #[Route('/shop/pickupdelivery', name: 'app_shop_pickup')]
    public function pickupDelivery(CartService $cartService): Response
    {
        // On vide le panier seulement maintenant
        $cartService->clear();
        return $this->render('shop/order/pickup.html.twig', [
            'current_page' => 'pickupdelivery',
        ]);
    }

    #[Route('/shop/paylinedelivery', name: 'app_shop_paylinedelivery')]
    public function paylineDelivery(): Response
    {
        return $this->render('shop/order/payline.html.twig', [
            'current_page' => 'paylinedelivery',
        ]);
    }


}
