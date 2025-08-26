<?php

namespace App\Entity;

use App\Repository\OrdersArticlesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdersArticlesRepository::class)]
class OrdersArticles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ordersArticles')]
    private ?Orders $order_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Articles $article_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?Orders
    {
        return $this->order_id;
    }

    public function setOrderId(?Orders $order_id): static
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getArticleId(): ?Articles
    {
        return $this->article_id;
    }

    public function setArticleId(?Articles $article_id): static
    {
        $this->article_id = $article_id;

        return $this;
    }
}
