<?php

namespace App\Entity\Data;

use App\Entity\Order\Order;
use App\Entity\Traits\HasGedmoTimestampTrait;
use App\Repository\Data\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[UniqueEntity('reference')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['purchase' => Purchase::class, 'credit' => Credit::class, 'debit' => Debit::class])]
abstract class Transaction
{
    use HasGedmoTimestampTrait;
    public const OPERATION = 'TRANSACTION';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(groups: ['Default', 'new'])]
    #[Assert\GreaterThan(0, groups: ['Default', 'new'])]
    protected int $points = 0;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(groups: ['Default', 'new'])]
    protected Account $account;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    protected Wallet $wallet;

    #[ORM\ManyToOne(inversedBy: 'transactions')]
    protected ?Order $order = null;

    abstract public function getType(): string;

    public function getReference(): string
    {
        return sprintf('%s - %08d', $this->getType(), $this->id);
    }

    public function __toString(): string
    {
        return $this->getReference();
    }

    public function __construct(Wallet $wallet, int $points)
    {
        $this->createdAt = new \DateTime();
        $this->wallet = $wallet;
        $this->account = $wallet->getAccount();
        $this->account->getTransactions()->add($this);
        $this->points = $points;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getWallet(): Wallet
    {
        return $this->wallet;
    }

    public function setWallet(Wallet $wallet): static
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }
}
