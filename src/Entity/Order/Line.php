<?php

namespace App\Entity\Order;

use App\Entity\Shop\Product;
use App\Entity\Traits\HasIdTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`order_line`')]
class Line
{
    use HasIdTrait;

    #[ORM\Column(type: Types::INTEGER)]
    private int $amount;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $order;

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function decreaseQuantity(): static
    {
        --$this->quantity;

        return $this;
    }

    public function increaseQuantity(): static
    {
        ++$this->quantity;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->quantity * $this->amount;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        $this->amount = $product->getAmount();

        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): static
    {
        $this->order = $order;

        return $this;
    }
}
