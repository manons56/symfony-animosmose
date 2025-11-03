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
use App\Service\CartService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller handling user orders: listing, viewing, deleting, and delivery selection.
 */
final class OrderController extends AbstractController
{
    /**
     * Show all completed orders for the currently logged-in user.
     * @IsGranted("ROLE_USER") ensures only logged-in users can access this.
     */
    #[Route('/shop/order', name: 'app_shop_order')]
    #[IsGranted('ROLE_USER')]
    public function index(OrdersRepository $ordersRepository): Response
    {
        $user = $this->getUser();
        // Fetch only orders belonging to the current user
        $orders = $ordersRepository->findCompletedOrdersForUser($user);

        return $this->render('shop/order/index.html.twig', [
            'orders' => $orders,
            'current_page' => 'order',
        ]);
    }

    /**
     * Show a single order and allow the user to choose a delivery method.
     */
    #[Route('/shop/order/{id}', name: 'app_shop_order_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Request $request, Orders $order, EntityManagerInterface $em): Response
    {
        // Ensure the logged-in user is the owner of the order
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à cette commande.');
        }

        // Define delivery options with labels, prices, and descriptions
        $deliveryOptions = [
            'relay' => ['label' => 'Point relais', 'price' => 8.00, 'description' => 'Retrait dans un point relais proche de chez vous avec MondialRelay.'],
            'home' => ['label' => 'Livraison à domicile', 'price' => 5.00, 'description' => 'Livraison directement à votre domicile, 1 fois par mois. Livraison possible dans le 56, 35 et 44.'],
            'pickup' => ['label' => 'Retrait sur place', 'price' => 0.00, 'description' => 'Vous venez récupérer votre commande en magasin, gratuitement.'],
        ];

        $form = $this->createForm(DeliveryType::class); // Create a form to select delivery method
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $selectedDelivery = $data['delivery_method'];

            // Save selected delivery method and mark order as Pending
            $order->setDeliveryMethod($selectedDelivery);
            $order->setStatus(OrderStatus::Pending);
            $em->flush();

            // Redirection logic based on delivery method
            if ($selectedDelivery === 'home') {
                return $this->redirectToRoute('app_shop_homedelivery');
            }

            if ($selectedDelivery === 'pickup') {
                return $this->redirectToRoute('app_shop_pickup');
            }

            // For relay delivery, redirect to Payline payment page
            if ($selectedDelivery === 'relay') {
                // Temporarily store the relay order in session
                $request->getSession()->set('relay_pending_order', [
                    'order_id' => $order->getId(),
                ]);

                return $this->redirectToRoute('checkout_pay', ['orderId' => $order->getId()]);
            }
        }

        // Render the order view with the delivery form
        return $this->render('shop/order/show.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
            'current_page' => 'order',
            'deliveryOptions' => $deliveryOptions,
        ]);
    }

    /**
     * Delete an order if it's in Pending status.
     */
    #[Route('/shop/order/{id}/delete', name: 'app_shop_order_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Orders $order, EntityManagerInterface $em): Response
    {
        // Ensure the order belongs to the logged-in user
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        // Check CSRF token validity for security
        if ($this->isCsrfTokenValid('delete_order_' . $order->getId(), $request->request->get('_token'))) {
            if ($order->getStatus() === OrderStatus::Pending) {
                $em->remove($order); // Remove order from database
                $em->flush();
                $this->addFlash('success', 'Commande supprimée.');
            } else {
                $this->addFlash('warning', 'Seules les commandes en attente peuvent être supprimées.');
            }
        }

        return $this->redirectToRoute('app_shop_order');
    }

    /**
     * API endpoint to save the selected Mondial Relay point for relay delivery.
     */
    #[Route('/api/mondial-relay/save', name: 'api_mondial_relay_save', methods: ['POST'])]
    public function savePointRelay(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true); // Decode JSON payload

        // Fetch the pending order of the logged-in user
        $order = $em->getRepository(Orders::class)->findOneBy([
            'user' => $this->getUser(),
            'status' => OrderStatus::Pending
        ]);

        if ($order) {
            $order->setDeliveryMethod('relay'); // Mark order as relay delivery
            // Optionally save relay point info in the order
            // $order->setRelayInfo(json_encode($data));
            $em->flush();
        }

        return $this->json(['success' => true]); // Return JSON response indicating success
    }

    /**
     * Home delivery confirmation page. Clears the cart once the order is placed.
     */
    #[Route('/shop/homedelivery', name: 'app_shop_homedelivery')]
    public function homeDelivery(CartService $cartService): Response
    {
        $cartService->clear(); // Clear cart only at this step
        return $this->render('shop/order/homedelivery.html.twig', [
            'current_page' => 'homedelivery',
        ]);
    }

    /**
     * Pickup delivery confirmation page. Clears the cart once the order is placed.
     */
    #[Route('/shop/pickupdelivery', name: 'app_shop_pickup')]
    public function pickupDelivery(CartService $cartService): Response
    {
        $cartService->clear(); // Clear cart
        return $this->render('shop/order/pickup.html.twig', [
            'current_page' => 'pickupdelivery',
        ]);
    }

    /**
     * Relay/Payline delivery confirmation page. Render the payment page.
     */
    #[Route('/shop/paylinedelivery', name: 'app_shop_paylinedelivery')]
    public function paylineDelivery(): Response
    {
        return $this->render('shop/order/payline.html.twig', [
            'current_page' => 'paylinedelivery',
        ]);
    }
}
