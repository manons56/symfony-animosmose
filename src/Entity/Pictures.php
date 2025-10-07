<?php

namespace App\Entity;

use App\Repository\PicturesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: PicturesRepository::class)]
class Pictures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $filename = null;


    // Propriété pour stocker temporairement le fichier uploadé (non en base)
    private ?UploadedFile $file = null;

    #[ORM\ManyToOne(targetEntity: Products::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Products $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }


    // Getter et setter pour la propriété non persistée "file"
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(?UploadedFile $file): self
    {
        $this->file = $file;
        return $this;
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

}
