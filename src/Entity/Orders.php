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


    #[ORM\Column(length: 255, unique: true)] // unique:true creates a unique index
    private ?string $reference = null;



    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->date = new \DateTimeImmutable();
        $this->total = 0;
        $this->status = OrderStatus::Pending;
        $this->reference = uniqid('CMD-'); // Automatically generates a unique reference

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

    public function getArticles(): Collection  // returns the full collection of articles linked to this order
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self  // allows adding an article to the order
    {
        if (!$this->articles->contains($article)) { // Checks if the article is already in the collection; if not, adds it
            $this->articles[] = $article; // Adds the article to the $articles collection
            $article->setOrder($this);
            $this->total += $article->getPrice() * $article->getQuantity();

        }
        return $this;
    }

    public function getTotal(): float { return $this->total; }

    // getTotal() simply reads the total
    // The total is managed automatically by the internal logic (addArticle()), so it does not need to be directly editable
    // therefore no setter is needed

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

    public function getDeliveryPrice(): float
    {
        return match ($this->deliveryMethod) {
            'relay' => 8.00,
            'home' => 5.00,
            'pickup' => 0.00,
            default => 0.00,
        };
    }

    public function getTotalWithDelivery(): float
    {
        return $this->getTotal() + $this->getDeliveryPrice();
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

}
