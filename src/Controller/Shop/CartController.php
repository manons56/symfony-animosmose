<?php

namespace App\Controller\Shop;

use App\Entity\User;
use App\Entity\Address;
use App\Entity\Orders;
use App\Entity\Articles;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Repository\ProductsRepository;
use App\Repository\VariantsRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Controller to manage the shopping cart: display, add, remove items, and checkout
 */
class CartController extends AbstractController
{
    /**
     * Display the shopping cart page
     */
    #[Route('/shop/cart', name: 'app_shop_cart')]
    public function index(CartService $cartService): Response
    {
        return $this->render('shop/cart/index.html.twig', [
            'items' => $cartService->getCart(),   // pass all cart items to Twig
            'total' => $cartService->getTotal(),  // pass the total cart amount
            'current_page' => 'cart',
        ]);
    }

    /**
     * Add a variant/product to the cart (POST only)
     */
    #[Route('/shop/cart/add/{variantId}', name:'app_shop_cart_add', methods: ['POST'])]
    public function add(
        int $variantId,
        Request $request,
        CartService $cartService,
        CsrfTokenManagerInterface $csrfTokenManager,
        VariantsRepository $variantRepository
    ): Response
    {
        $submittedToken = $request->request->get('_token');

        // CSRF token validation
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('cart_add_' . $variantId, $submittedToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        // Retrieve the variant
        $variant = $variantRepository->find($variantId);
        if (!$variant) {
            throw $this->createNotFoundException('Variant introuvable.');
        }

        $product = $variant->getProduct();

        // Check stock availability
        if ($product->isOutOfStock()) {
            $this->addFlash('error', 'Ce produit est en rupture de stock.');
            return $this->redirectToRoute('app_product_list');
        }

        // Handle optional custom text for customizable products
        $customText = null;
        if ($product->isCustomizable()) {
            $customText = trim((string)$request->request->get('customText'));

            // Validate custom text length
            if ($customText === '' || strlen($customText) > 10) {
                $this->addFlash('error', 'Merci de remplir correctement le champ personnalisé (10 caractères max).');
                return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
            }
        }

        // Add item to cart
        $cartService->add($variantId, $customText);
        $this->addFlash('success', 'Produit ajouté au panier avec succès !');

        return $this->redirectToRoute('app_shop_cart');
    }

    /**
     * Remove an item/variant from the cart (POST only)
     */
    #[Route('/shop/cart/remove/{id}', name:'app_shop_cart_remove', methods: ['POST'])]
    public function remove(
        int $id,
        Request $request,
        CartService $cartService,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response
    {
        $submittedToken = $request->request->get('_token');

        // Validate CSRF token
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('cart_remove_' . $id, $submittedToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        // Remove item from cart
        $cartService->remove($id);

        return $this->redirectToRoute('app_shop_cart');
    }

    /**
     * Trigger checkout page (GET)
     * - Checks if user is logged in
     * - Checks that the cart is not empty
     * - Provides CSRF token for next POST step
     */
    #[Route('/shop/cart/checkout/trigger', name: 'app_shop_cart_checkout_trigger', methods: ['GET'])]
    public function checkoutTrigger(CartService $cartService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (empty($cartService->getCart())) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_shop_cart');
        }

        return $this->render('shop/cart/trigger_checkout.html.twig', [
            'csrf_token' => $this->container->get('security.csrf.token_manager')->getToken('cart_checkout')->getValue(),
        ]);
    }

    /**
     * Process checkout (POST)
     * - Creates an order and associated articles
     * - Clears the cart
     */
    #[Route('/shop/cart/checkout', name:'app_shop_cart_checkout', methods: ['POST'])]
    public function checkout(CartService $cartService, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $cartItems = $cartService->getCart();

        if (empty($cartItems)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_shop_cart');
        }

        // Create new order entity
        $order = new Orders();
        $order->setUser($this->getUser());
        $address = $this->getUser()->getAddress();
        $order->setAddressId($address);

        // Add each cart item as an Article entity
        foreach ($cartItems as $item) {
            $variant = $item['variant'];

            $article = new Articles();
            $article->setVariantId($variant)
                ->setPrice($variant->getPrice())
                ->setQuantity($item['quantity'])
                ->setOrder($order);

            // Optional custom text if product is customizable
            if ($variant->getProduct()->isCustomizable() && !empty($item['customText'])) {
                $article->setCustomText($item['customText']);
            }

            $order->addArticle($article);
        }

        // Persist order and its articles
        $manager->persist($order);
        $manager->flush();

        // Clear the cart session
        $cartService->clear();

        // Flash message in French confirming order creation
        $this->addFlash('success', 'Commande créée avec succès !');

        return $this->redirectToRoute('app_shop_order_show', ['id' => $order->getId()]);
    }
}
