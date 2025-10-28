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
    private ?string $price = null;


    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Orders $order = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $customText = null;



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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOrder(): ?Orders
    {
        return $this->order;
    }

    public function setOrder(?Orders $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function getCustomText(): ?string
    {
        return $this->customText;
    }

    public function setCustomText(?string $customText): self
    {
        $this->customText = $customText;
        return $this;
    }
}
