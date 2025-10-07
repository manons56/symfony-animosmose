<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    private ?UploadedFile $imageFile = null;

    /**
     * @var Collection<int, Pictures>
     */
    #[ORM\OneToMany(targetEntity: Pictures::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $images;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $capacity = null;

    #[ORM\Column(type: 'text', nullable: true)]
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
    private ?Categories $category = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isOutOfStock = null;

    /**
     * @var Collection<int, Variants>
     */
    #[ORM\OneToMany(targetEntity: Variants::class, mappedBy: 'product', cascade: ['persist', 'remove'])]
    private Collection $variants;

    public function __construct()
    {
        $this->variants = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    // ---------- Getters / Setters classiques ----------

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImageFile(): ?UploadedFile
    {
        return $this->imageFile;
    }

    public function setImageFile(?UploadedFile $imageFile): self
    {
        $this->imageFile = $imageFile;
        return $this;
    }

    /**
     * @return Collection<int, Pictures>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Pictures $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }
        return $this;
    }

    public function removeImage(Pictures $image): self
    {
        if ($this->images->removeElement($image)) {
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }
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

    public function setCapacity(?string $capacity): static
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

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): static
    {
        $this->category = $category;
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
            $variant->setProduct($this);
        }
        return $this;
    }

    public function removeVariant(Variants $variant): static
    {
        if ($this->variants->removeElement($variant)) {
            if ($variant->getProduct() === $this) {
                $variant->setProduct(null);
            }
        }
        return $this;
    }

    // ---------- Champ temporaire pour EasyAdmin ----------

    /**
     * Champ temporaire pour la catégorie principale (non mappé)
     *
     * @var int|null
     */
    private ?int $categoryParent = null;

    public function getCategoryParent(): ?int
    {
        return $this->categoryParent;
    }

    public function setCategoryParent(?int $categoryParent): self
    {
        $this->categoryParent = $categoryParent;
        return $this;
    }
}
