<?php
namespace App\Service;

use App\Repository\VariantsRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * CartService
 * --------------------------
 * This service manages the shopping cart stored in the user session.
 *
 * Features:
 * 1. Add items to the cart, with optional custom text.
 * 2. Remove items from the cart.
 * 3. Retrieve all cart items with quantity, custom text, and total price per item.
 * 4. Calculate the total cart price.
 * 5. Clear the cart entirely.
 */
class CartService
{
    private RequestStack $requestStack;
    private VariantsRepository $variantRepo;

    /**
     * Constructor: injects the RequestStack (for session) and the Variants repository
     */
    public function __construct(RequestStack $requestStack, VariantsRepository $variantRepo)
    {
        $this->requestStack = $requestStack;
        $this->variantRepo = $variantRepo;
    }

    /**
     * Private helper: get the current session from the request stack
     */
    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    /**
     * Add a product variant to the cart, optionally with a custom text
     *
     * @param int $variantId The ID of the product variant
     * @param string|null $customText Optional custom text for the variant
     *
     * @throws NotFoundHttpException if the variant does not exist
     */
    public function add(int $variantId, ?string $customText = null): void
    {
        $variant = $this->variantRepo->find($variantId);
        if (!$variant) {
            throw new NotFoundHttpException("Variant $variantId does not exist.");
        }

        $session = $this->getSession();
        $cart = $session->get('cart', []);

        // Ensure each variant in the cart is stored as an array with quantity and custom text
        if (!isset($cart[$variantId]) || !is_array($cart[$variantId])) {
            $cart[$variantId] = [
                'quantity' => 0,
                'customText' => null,
            ];
        }

        // Increment quantity (capped at 100)
        if ($cart[$variantId]['quantity'] < 100) {
            $cart[$variantId]['quantity']++;
        }

        // Update custom text if provided
        if ($customText !== null) {
            $cart[$variantId]['customText'] = $customText;
        }

        $session->set('cart', $cart);
    }

    /**
     * Remove a product variant from the cart
     *
     * @param int $variantId The ID of the product variant to remove
     */
    public function remove(int $variantId): void
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$variantId])) {
            unset($cart[$variantId]);
            $session->set('cart', $cart);
        }
    }

    /**
     * Retrieve all items in the cart
     *
     * Returns an array of items, each with:
     * - variant entity
     * - quantity
     * - customText
     * - total price (quantity * variant price)
     *
     * Handles session cleanup if a variant no longer exists in the database.
     */
    public function getCart(): array
    {
        $session = $this->getSession();
        $cart = $session->get('cart', []);
        $items = [];

        foreach ($cart as $variantId => $data) {
            // Convert legacy int values to array structure for compatibility
            if (!is_array($data)) {
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
                // Remove variants that no longer exist
                unset($cart[$variantId]);
                $session->set('cart', $cart);
            }
        }

        return $items;
    }

    /**
     * Calculate the total price of all items in the cart
     *
     * @return float Total cart value
     */
    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getCart() as $item) {
            $total += $item['total'];
        }
        return $total;
    }

    /**
     * Clear the entire cart from the session
     */
    public function clear(): void
    {
        $this->getSession()->remove('cart');
    }
}
