<?php

namespace App\Entity;

use App\Enum\OrderStatus;
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
    private ?OrderStatus $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address_id = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: Articles::class, cascade:["persist", "remove"])]
    private Collection $articles;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private string $total;

    #[ORM\Column(type: 'boolean')]
    private bool $archived = false;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $deliveryMethod = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->date = new \DateTimeImmutable();
        $this->total = 0;
        $this->status = OrderStatus::Pending;

    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): static
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
            $article->setOrder($this);
            $this->total += $article->getPrice() * $article->getQuantity();

        }
        return $this;
    }

    public function getTotal(): float { return $this->total; }

    // getTotal() permet simplement de lire le total
    //Le total est géré automatiquement par la logique interne (addArticle()), donc il n’a pas besoin d’être modifiable directement.
    // donc pas de setter nécessaire

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;
        return $this;
    }

    public function getDeliveryMethod(): ?string
    {
        return $this->deliveryMethod;
    }

    public function setDeliveryMethod(?string $deliveryMethod): self
    {
        $this->deliveryMethod = $deliveryMethod;

        return $this;
    }

}
