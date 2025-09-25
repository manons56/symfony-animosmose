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

            // --- Définir le status en fonction du mode de livraison ---
            if ($selectedDelivery === 'relay') {
                // Mondial Relay → paiement via Payline
                $order->setStatus(OrderStatus::Pending);
            } else {
                // Livraison à domicile ou retrait sur place → attente paiement liquide/chèque
                $order->setStatus(OrderStatus::Pending);
            }

            $em->flush();

            $this->addFlash('success', "Mode de livraison choisi : $selectedDelivery");
            return $this->redirectToRoute('app_shop_order_show', ['id' => $order->getId()]);
        }

        return $this->render('shop/order/show.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
            'current_page' => 'order',
            'deliveryOptions' => $deliveryOptions,  // <-- Ajout ici
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
}
