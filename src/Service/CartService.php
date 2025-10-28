<?php
namespace App\Service;

use App\Repository\VariantsRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CartService
{
    private RequestStack $requestStack;
    private VariantsRepository $variantRepo;

    public function __construct(RequestStack $requestStack, VariantsRepository $variantRepo)
    {
        $this->requestStack = $requestStack;
        $this->variantRepo = $variantRepo;
    }

    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    // Ajoute un article avec option customText
    public function add(int $variantId, ?string $customText = null): void
    {
        $variant = $this->variantRepo->find($variantId);
        if (!$variant) {
            throw new NotFoundHttpException("La variante $variantId n'existe pas.");
        }

        $session = $this->getSession();
        $cart = $session->get('cart', []);

        // Toujours stocker comme tableau
        if (!isset($cart[$variantId]) || !is_array($cart[$variantId])) {
            $cart[$variantId] = [
                'quantity' => 0,
                'customText' => null,
            ];
        }

        // Incrémente la quantité
        if ($cart[$variantId]['quantity'] < 100) {
            $cart[$variantId]['quantity']++;
        }

        // Met à jour le texte personnalisé si fourni
        if ($customText !== null) {
            $cart[$variantId]['customText'] = $customText;
        }

        $session->set('cart', $cart);
    }

    public function remove(int $variantId): void
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$variantId])) {
            unset($cart[$variantId]);
            $session->set('cart', $cart);
        }
    }

    public function getCart(): array
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);
        $items = [];

        foreach ($cart as $variantId => $data) {
            if (!is_array($data)) {
                // Convertir les anciens int en tableau pour compatibilité
                $data = [
                    'quantity' => $data,
                    'customText' => null,
                ];
                $cart[$variantId] = $data;
                $session->set('cart', $cart);
            }

            $variant = $this->variantRepo->find($variantId);
            if ($variant) {
                $items[] = [
                    'variant' => $variant,
                    'quantity' => $data['quantity'],
                    'customText' => $data['customText'] ?? null,
                    'total' => $variant->getPrice() * $data['quantity'],
                ];
            } else {
                // Supprime les variantes supprimées de la session
                unset($cart[$variantId]);
                $session->set('cart', $cart);
            }
        }

        return $items;
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['total'];
        }
        return $total;
    }

    public function clear(): void
    {
        $this->getSession()->remove('cart');
    }

}
