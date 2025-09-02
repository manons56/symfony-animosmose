<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersRepository::class)]
class Orders
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address_id = null;

    #[ORM\ManyToMany(targetEntity: Articles::class, inversedBy:"orders", cascade:["persist"])]
    private Collection $articles;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private float $total;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->date = new \DateTimeImmutable();
        $this->total = 0;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): static
    {
        $this->user_id = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAddressId(): ?Address
    {
        return $this->address_id;
    }

    public function setAddressId(?Address $address_id): static
    {
        $this->address_id = $address_id;

        return $this;
    }

    public function getArticles(): Collection  //renvoie la collection complète d’articles liés à cette commande.
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self  // permet d’ajouter un article à la commande.
    {
        if (!$this->articles->contains($article)) { //Vérifie si l’article est déjà dans la collection, si non, on l'ajoute
            $this->articles[] = $article; //Ajoute l’article à la collection $articles.
            $this->total += $article->getPrice() * $article->getQuantity();
            $article->addOrder($this); //avec une relation bidirectionnelle entre Order et Article, il faut aussi dire à l’article qu’il appartient à cette commande.
                                        // Donc on appelle addOrder() sur l’article.
        }
        return $this;
    }

    public function getTotal(): float { return $this->total; }

    // getTotal() permet simplement de lire le total
    //Le total est géré automatiquement par la logique interne (addArticle()), donc il n’a pas besoin d’être modifiable directement.
    // donc pas de setter nécessaire

}
