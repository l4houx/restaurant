<?php

namespace App\Entity\Data;

use App\Entity\Company\Member;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Entity\User;
use App\Repository\Data\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[UniqueEntity('reference')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Account implements \Stringable
{
    use HasIdTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;
    // use SoftDeleteableEntity;

    #[ORM\OneToOne(mappedBy: 'account')]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'account')]
    private ?Member $member = null;

    /**
     * @var Collection<int, Wallet>
     */
    #[ORM\OneToMany(targetEntity: Wallet::class, mappedBy: 'account', cascade: ['persist'])]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    #[Groups(['read'])]
    private Collection $wallets;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'account', cascade: ['persist'])]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $transactions;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->wallets = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null !== $this->user) {
            return sprintf('User : %s', $this->user->getFullName());
        }

        return sprintf('Company : %s', $this->member->getName());
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getOwner(): User|Member
    {
        return $this->user ?? $this->member;
    }

    /**
     * @return Collection<int, Wallet>
     */
    public function getWallets(): Collection
    {
        return $this->wallets;
    }

    /**
     * @return Collection<int, Wallet>
     */
    public function getExpiredWallets(): Collection
    {
        return $this->wallets->filter(fn (Wallet $wallet) => $wallet->isExpired() && $wallet->getBalance() > 0);
    }

    /**
     * @return Collection<int, Wallet>
     */
    public function getRemainingWallets(): Collection
    {
        return $this->wallets->filter(fn (Wallet $wallet) => !$wallet->isExpired() && $wallet->getBalance() > 0);
    }

    public function getBalance(): int
    {
        return intval(
            array_sum(
                $this->getRemainingWallets()->map(fn (Wallet $wallet) => $wallet->getBalance())->toArray()
            )
        );
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
}
