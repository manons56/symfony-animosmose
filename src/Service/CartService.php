<?php
namespace App\Service;

use App\Repository\VariantsRepository;         // <- Pour récupérer les variantes depuis la BDD
use Symfony\Component\HttpFoundation\RequestStack; // <- Pour lire/écrire dans la session

class CartService
{
    private $session;               // <- On gardera la session ici
    private VariantsRepository $variantRepo;   // <- Et le repo des variantes ici

    //

    public function __construct(                    // <- Injection de dépendances par le conteneur Symfony
        RequestStack $requestStack,                   //    Symfony t'injecte la session active
        VariantsRepository $variantRepo        //    et le repository des variantes
    ) {
        $this->session =  $requestStack->getSession();     // <- On stocke la session dans la propriété
        $this->variantRepo = $variantRepo;           // <- Idem pour le repo
    }

    public function add(int $variantId): void        // <- Méthode publique pour AJOUTER 1 unité d'une variante
    {
        $cart = $this->session->get('cart', []);     // <- On lit le panier depuis la session, [] si vide
        if (!isset($cart[$variantId])) {             // <- Si cette variante n'est pas encore dans le panier
            $cart[$variantId] = 0;                   // <- On initialise sa quantité à 0
        }
        $cart[$variantId]++;                         // <- On incrémente de 1 la quantité de cette variante
        $this->session->set('cart', $cart);          // <- On réécrit le panier en session
    }

    public function remove(int $variantId): void     // <- Méthode publique pour SUPPRIMER complètement la variante
    {
        $cart = $this->session->get('cart', []);     // <- Récupère le panier actuel
        if (isset($cart[$variantId])) {              // <- Si la variante est présente
            unset($cart[$variantId]);                // <- On la retire du tableau
        }
        $this->session->set('cart', $cart);          // <- On sauvegarde le panier modifié
    }

    public function getCart(): array                 // <- Renvoie une liste exploitable pour l'affichage
    {
        $cart = $this->session->get('cart', []);     // <- { variantId => quantity, ... }
        $items = [];                                  // <- On va construire un tableau riche
        foreach ($cart as $variantId => $quantity) {  // <- Pour chaque ligne du panier
            $variant = $this->variantRepo->find($variantId); // <- On charge l'entité ProductVariant
            if ($variant) {                           // <- Si elle existe
                $items[] = [                          // <- On pousse un "item" prêt pour Twig
                    'variant' => $variant,            //    l'entité variante (pour nom, prix, SKU, etc.)
                    'quantity' => $quantity,          //    la quantité demandée
                    'total' => $variant->getPrice() * $quantity //    le total ligne (= prix * qty)
                ];
            }
        }
        return $items;                                // <- Tableau d'items pour le template
    }

    public function getTotal(): float                 // <- Total du panier (somme des totaux ligne)
    {
        $total = 0;                                   // <- Accumulateur
        foreach ($this->getCart() as $item) {         // <- On parcourt les items "riches"
            $total += $item['total'];                 // <- On additionne
        }
        return $total;                                // <- Total final (type float)
    }

    public function clear(): void
    {
        $this->session->remove('cart'); // permet de vider le panier après la création d’une Order.
    }

}
