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
    #[ORM\JoinColumn(nullable: false)]
    private ?Products $product = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $contenance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $color = null;


    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDefault = null;


    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private ?bool $isOutOfStock = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): self
    {
        $this->product = $product;
        return $this;
    }



    public function getContenance(): ?string
    {
        return $this->contenance;
    }

    public function setContenance(string $contenance): static
    {
        $this->contenance = $contenance;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;
        return $this;
    }


    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;
        return $this;
    }





   /* public function __toString(): string
    {
        return $this->getDisplayName();
    }
   */


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
        return $this->price;
    }

    // Setter pour stocker en centimes
    public function setPriceEuros(float $priceEuros): static
    {
        $this->price = $priceEuros;
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

    public function isOutOfStock(): ?bool
    {
        return $this->isOutOfStock;
    }

    public function setIsOutOfStock(?bool $isOutOfStock): static
    {
        $this->isOutOfStock = $isOutOfStock;
        return $this;
    }
}
