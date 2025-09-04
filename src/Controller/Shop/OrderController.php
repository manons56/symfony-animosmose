<?php

namespace App\Controller\Shop;

use App\Entity\Orders;
use App\Repository\OrdersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $orders = $ordersRepository->findBy(['user' => $user]);


        return $this->render('shop/order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/shop/order/{id}', name:'app_shop_order_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Orders $order): Response
    {
        // l’utilisateur ne peut voir que ses propres commandes
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n’avez pas accès à cette commande.');
        }

        return $this->render('shop/order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
