<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    //persist → on enregistre (persist) un produit, Doctrine enregistre l’image associée si elle est nouvelle.
    //remove → on supprime(remove) un produit, Doctrine supprime aussi l’image associée automatiquement.
    #[ORM\JoinColumn(nullable: false)]
    private ?Pictures $img_id = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $capacity = null;

    #[ORM\Column(type: 'text')]
    private ?string $composition = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $analytics_components = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $nutritionnal_additive = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isNew = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isBestseller = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $category_id = null;

    /**
     * @var Collection<int, Variants>
     */
    #[ORM\OneToMany(targetEntity: Variants::class, mappedBy: 'product_id', cascade: ['persist', 'remove'])]
    private Collection $variants;

    public function __construct()
    {
        $this->variants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImgId(): ?Pictures
    {
        return $this->img_id;
    }

    public function setImgId(Pictures $img_id): static
    {
        $this->img_id = $img_id;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCapacity(): ?string
    {
        return $this->capacity;
    }

    public function setCapacity(string $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getComposition(): ?string
    {
        return $this->composition;
    }

    public function setComposition(string $composition): static
    {
        $this->composition = $composition;

        return $this;
    }

    public function getAnalyticsComponents(): ?string
    {
        return $this->analytics_components;
    }

    public function setAnalyticsComponents(?string $analytics_components): static
    {
        $this->analytics_components = $analytics_components;

        return $this;
    }

    public function getNutritionnalAdditive(): ?string
    {
        return $this->nutritionnal_additive;
    }

    public function setNutritionnalAdditive(?string $nutritionnal_additive): static
    {
        $this->nutritionnal_additive = $nutritionnal_additive;

        return $this;
    }

    public function isNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(?bool $isNew): static
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function isBestseller(): ?bool
    {
        return $this->isBestseller;
    }

    public function setIsBestseller(?bool $isBestseller): static
    {
        $this->isBestseller = $isBestseller;

        return $this;
    }

    public function getCategoryId(): ?Categories
    {
        return $this->category_id;
    }

    public function setCategoryId(?Categories $category_id): static
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * @return Collection<int, Variants>
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(Variants $variant): static
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setProductId($this);
        }

        return $this;
    }

    public function removeVariant(Variants $variant): static
    {
        if ($this->variants->removeElement($variant)) {
            // set the owning side to null (unless already changed)
            if ($variant->getProductId() === $this) {
                $variant->setProductId(null);
            }
        }

        return $this;
    }
}
