<?php

namespace App\Entity\Data;

use App\Repository\Data\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase extends Transaction
{
    public const OPERATION = 'ACH';
    public const MODE_CHECK = 'CHECK';
    public const MODE_BANK_WIRE = 'BANK';

    #[ORM\Column(type: Types::STRING)]
    private string $state = 'pending';

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $internReference = null;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotBlank(groups: ['Default' => 'new'])]
    private string $mode;

    public function __construct()
    {
    }

    public function prepare(): static
    {
        $wallet = new Wallet($this->account, new \DateTimeImmutable('2 year first day of next month midnight'));
        $this->createdAt = new \DateTime();
        $this->wallet = $wallet;
        $wallet->setPurchase($this);

        return $this;
    }

    public function setAccount(Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
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

    public function getInternReference(): ?string
    {
        return $this->internReference;
    }

    public function setInternReference(?string $internReference): static
    {
        $this->internReference = $internReference;

        return $this;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): static
    {
        $this->mode = $mode;

        return $this;
    }

    public function getType(): string
    {
        return 'Purchase';
    }
}
