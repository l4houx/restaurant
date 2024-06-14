<?php

namespace App\Entity\Data;

use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasExpiredAtTrait;
use App\Entity\Traits\HasGedmoTimestampTrait;
use App\Entity\Traits\HasIdTrait;
use App\Repository\Data\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Wallet implements \Stringable
{
    use HasIdTrait;
    use HasGedmoTimestampTrait;
    use HasDeletedAtTrait;
    // use SoftDeleteableEntity;
    use HasExpiredAtTrait;

    #[ORM\Column(type: Types::INTEGER)]
    private int $balance = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Purchase $purchase = null;

    #[ORM\ManyToOne(inversedBy: 'wallets', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'wallet', cascade: ['persist'])]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $transactions;

    public function __toString(): string
    {
        return $this->getReference();
    }

    public function __construct(Account $account, \DateTimeImmutable $expiredAt)
    {
        $this->transactions = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->expiredAt = $expiredAt;
        $this->account = $account;
        if (!$account->getWallets()->contains($this)) {
            $account->getWallets()->add($this);
        }
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function addTransaction(Transaction $transaction): void
    {
        $this->balance += $transaction->getPoints();
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

    public function getPurchase(): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): static
    {
        $this->purchase = $purchase;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function getReference(): string
    {
        return sprintf('%08d', $this->id);
    }
}
