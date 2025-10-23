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



class CartController extends AbstractController
{
    #[Route('/shop/cart', name: 'app_shop_cart')] // --> permet d'afficher le panier
        // Cette méthode sera accessible via /cart
        // Le nom de la route est "cart_index" (on s’en sert pour redirect ou path())
    public function index(CartService $cartService): Response
    {
        return $this->render('shop/cart/index.html.twig', [
            // render() est un helper qui génère une réponse HTML à partir d’un template Twig
            'items' => $cartService->getCart(),
            // On passe la liste des articles du panier au template sous le nom "items"
            'total' => $cartService->getTotal(),
            // On passe aussi le total du panier
            'current_page' => 'cart',
        ]);
    }

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

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('cart_add_' . $variantId, $submittedToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        // On récupère la variante
        $variant = $variantRepository->find($variantId);

        if (!$variant) {
            throw $this->createNotFoundException('Variant introuvable.');
        }

        // On récupère le produit associé
        $product = $variant->getProduct();

        if ($product->isOutOfStock()) {
            $this->addFlash('error', 'Ce produit est en rupture de stock.');
            return $this->redirectToRoute('app_product_list');
        }

        // On ajoute la variante au panier
        $cartService->add($variantId);

        return $this->redirectToRoute('app_shop_cart');
    }



    #[Route('/shop/cart/remove/{id}', name:'app_shop_cart_remove', methods: ['POST'])] // --> permet de retirer un produit du panier, méthode POST uniquement
        // Route pour supprimer un produit/variant du panier
        // {id} correspond à l’identifiant du produit à retirer
    public function remove(
        int $id,
        Request $request,
        CartService $cartService,
        CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        // On récupère le token CSRF envoyé via le formulaire POST
        $submittedToken = $request->request->get('_token');

        // On vérifie que le token est valide pour éviter les attaques CSRF
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('cart_remove_' . $id, $submittedToken))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $cartService->remove($id);
        // On appelle la méthode remove() pour enlever l’élément de la session panier

        return $this->redirectToRoute('app_shop_cart');
        // Après suppression, on redirige encore vers la page panier
    }


    #[Route('/shop/cart/checkout/trigger', name: 'app_shop_cart_checkout_trigger', methods: ['GET'])]
    public function checkoutTrigger(CartService $cartService): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Vérifie que le panier n’est pas vide
        if (empty($cartService->getCart())) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_shop_cart');
        }

        // Redirige vers un contrôleur qui va vraiment valider la commande (en POST)
        return $this->render('shop/cart/trigger_checkout.html.twig', [
            'csrf_token' => $this->container->get('security.csrf.token_manager')->getToken('cart_checkout')->getValue(),
        ]);
    }

// par défaut, après le login, symfony cherche à retourner sur la page d'avant login, via un GET
// la route ci dessus, va permettre, avec le trigger_checkout.html.twig et son js, de créer une étape intermédiaire
// --> apres le login, le get va etre transformé en post
// la route ci dessous est alors appelé et peut etre utilisé correctement et on est redirigé vers la page de commande


    #[Route('/shop/cart/checkout', name:'app_shop_cart_checkout', methods: ['POST'])]// permet de valider le panier et transformer en commande
    public function checkout(CartService $cartService, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); // Bloque les non-connectés
        $cartItems = $cartService->getCart(); //On récupère le contenu du panier depuis la session via le CartService.

        if (empty($cartItems)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_shop_cart');
        }

        $order = new Orders();
        $order->setUser($this->getUser()); // ← ajoute l’utilisateur connecté
        $address = $this->getUser()->getAddress(); // récupère l'adresse de l'utilisateur
        $order->setAddressId($address);

        foreach ($cartItems as $item) { // on parcourt chaque item du panier
            $article = new Articles(); // on crée un article pour chaque item
            $article->setVariantId($item['variant'])
                ->setPrice($item['variant']->getPrice())
                ->setQuantity($item['quantity'])
                ->setOrder($order); // Important pour relier l’article à la commande
            //Sans setOrder() sur l’article, Doctrine ne sait pas quel order_id mettre → les articles ne sont pas liés → $order->getArticles() reste vide.

            $order->addArticle($article); // on ajoute l'article à la commande via addArticle() créé dans Orders.php
        }

        $manager->persist($order);
        // Avec cascade: persist, $manager->persist($order) suffit à Doctrine pour insérer à la fois la commande et tous les articles liés.

        $manager->flush();
        // Doctrine exécute toutes les requêtes SQL en base : Création des Articles, Création de la commande, Liaison ManyToMany entre Order et Articles

        //$cartService->clear();
        // on vide le panier dans la session
        $this->addFlash('success', 'Commande créée avec succès !');

        return $this->redirectToRoute('app_shop_order_show', ['id' => $order->getId()]);
        //Redirection vers la page de la commande.
        //L’utilisateur voit donc un récapitulatif avec tous les articles et le total.
    }
}
