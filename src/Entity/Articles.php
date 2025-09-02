<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Variants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
class Articles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Variants::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Variants $variant_id = null;

    #[ORM\Column(type:"integer")]
    private ?int $quantity = null;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private ?float $price = null;


    #[ORM\ManyToMany(targetEntity: Orders::class, mappedBy:"articles")]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVariantId(): ?Variants
    {
        return $this->variant_id;
    }

    public function setVariantId(?Variants $variant_id): static
    {
        $this->variant_id = $variant_id;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): static
    {
        if (!$this->orders->contains($order)) { //"Si cette commande n’est pas encore dans la collection, je l’ajoute."
            $this->orders[] = $order;
        }
        return $this;
    }
    //Si tu avais un setter classique,ça poserait un problème : on remplacerait toute la collection existante.
    //on veut juste ajouter une commande à la collection sans écraser celles déjà présentes.
    //D’où l’idée du addOrder : On ajoute une seule commande à la collection.
    //On garde toutes les commandes déjà présentes

}
