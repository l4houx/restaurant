<?php

namespace App\Entity\Order;

use App\Entity\User;
use App\Entity\Address;
use App\Entity\Shop\Product;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;
use App\Repository\Order\OrderRepository;
use Doctrine\Common\Collections\Collection;
use App\Entity\Traits\HasTimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    use HasIdTrait;
    use HasTimestampableTrait;

    #[ORM\Column(type: Types::STRING)]
    private string $state = 'cart';

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Line>
     */
    #[ORM\OneToMany(targetEntity: Line::class, mappedBy: 'order', cascade: ['persist'])]
    private Collection $lines;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?Address $address = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->lines = new ArrayCollection();
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Line>
     */
    public function getLines(): Collection
    {
        return $this->lines;
    }

    public function addProduct(Product $product): static
    {
        $lines = $this->lines->filter(fn (Line $line) => $line->getProduct() === $product);

        $line = $lines->first();

        if ($line === false) {
            $line = new Line();
            $line->setOrder($this);
            $line->setProduct($product);
            $this->lines->add($line);
        }

        $line->increaseQuantity();

        return $this;
    }

    public function getTotal(): int
    {
        return intval(
            array_sum(
                $this->lines->map(fn (Line $line) => $line->getTotal())->toArray()
            )
        );
    }

    public function getNumberOfProducts(): int
    {
        return intval(array_sum($this->lines->map(fn (Line $line) => $line->getQuantity())->toArray()));
    }

    public function getReference(): string
    {
        return sprintf("BCB%04d-%d", $this->id, $this->user->getId());
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }
}
