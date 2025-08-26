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
    private ?User $user_id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address_id = null;

    /**
     * @var Collection<int, OrdersArticles>
     */
    #[ORM\OneToMany(targetEntity: OrdersArticles::class, mappedBy: 'order_id')]
    private Collection $ordersArticles;

    public function __construct()
    {
        $this->ordersArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

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

    /**
     * @return Collection<int, OrdersArticles>
     */
    public function getOrdersArticles(): Collection
    {
        return $this->ordersArticles;
    }

    public function addOrdersArticle(OrdersArticles $ordersArticle): static
    {
        if (!$this->ordersArticles->contains($ordersArticle)) {
            $this->ordersArticles->add($ordersArticle);
            $ordersArticle->setOrderId($this);
        }

        return $this;
    }

    public function removeOrdersArticle(OrdersArticles $ordersArticle): static
    {
        if ($this->ordersArticles->removeElement($ordersArticle)) {
            // set the owning side to null (unless already changed)
            if ($ordersArticle->getOrderId() === $this) {
                $ordersArticle->setOrderId(null);
            }
        }

        return $this;
    }
}
