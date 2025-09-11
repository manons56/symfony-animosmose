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
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/shop/cart', name: 'app_shop_cart')] // --> permet d'afficher le panier
        // Cette méthode sera accessible via /cart
        // Le nom de la route est "cart_index" (on s’en sert pour redirect ou path())

    public function index(CartService $cartService): Response
        // On déclare la méthode "index"
        // Symfony injecte automatiquement CartService grâce à l’autowiring
        // On précise que la méthode retourne un objet Response
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


    #[Route('/shop/cart/add/{id}', name:'app_shop_cart_add')] // --> permet d'ajouter un produit au panier
    // Route pour ajouter un produit/variant au panier
    // {id} est un paramètre de route (ex: /cart/add/5)

    public function add(int $id, CartService $cartService): Response
    // Méthode add : reçoit l’id (converti automatiquement en int) et le service du panier
    {
        $cartService->add($id);
        // On appelle la méthode add() du CartService pour ajouter ce produit/variant

        return $this->redirectToRoute('app_shop_cart');
        // Après l’ajout, on redirige vers la page du panier (route "app_shop_cart")
    }


    #[Route('/shop/cart/remove/{id}', name:'app_shop_cart_remove')] // --> permet de retirer un produit du panier
    // Route pour supprimer un produit/variant du panier
    // {id} correspond à l’identifiant du produit à retirer

    public function remove(int $id, CartService $cartService): Response
    // Méthode remove : reçoit l’id et le service du panier
    {
        $cartService->remove($id);
        // On appelle la méthode remove() pour enlever l’élément de la session panier

        return $this->redirectToRoute('app_shop_cart');
        // Après suppression, on redirige encore vers la page panier
    }


    #[Route('/shop/cart/checkout', name:'app_shop_cart_checkout')] // permet de valider le panier et transformer en commande
    public function checkout(CartService $cartService, EntityManagerInterface $manager): Response
    {
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

        $manager->persist($order); // Avec cascade: persist, $manager->persist($order) suffit à Doctrine pour insérer à la fois la commande et tous les articles liés.
        $manager->flush(); // Doctrine exécute toutes les requêtes SQL en base : Création des Articles, Création de la commande, Liaison ManyToMany entre Order et Articles


        $cartService->clear(); // on vide le panier dans la session
        $this->addFlash('success', 'Commande créée avec succès !');


        return $this->redirectToRoute('app_shop_order');
        //Redirection vers la page de la commande.
        //L’utilisateur voit donc un récapitulatif avec tous les articles et le total.
    }

}
