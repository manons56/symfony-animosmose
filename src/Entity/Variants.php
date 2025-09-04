<?php

namespace App\Entity;

use App\Repository\VariantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VariantsRepository::class)]
class Variants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Products::class, inversedBy: 'variants')]
    private ?Products $product_id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDefault = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?Products
    {
        return $this->product_id;
    }

    public function setProductId(?Products $product_id): static
    {
        $this->product_id = $product_id;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function __toString(): string
    {
        return $this->label ?? 'Variant';
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceEuros(): float
    {
        return $this->price / 100;
    }

    // Setter pour stocker en centimes
    public function setPriceEuros(float $priceEuros): static
    {
        $this->price = (int)round($priceEuros * 100);
        return $this;
    }


    public function isDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(?bool $isDefault): static
    {
        $this->isDefault = $isDefault;

        return $this;
    }
}
