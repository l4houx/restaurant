<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Debit extends Transaction
{
    public const OPERATION = 'DEBIT';

    #[ORM\OneToOne]
    private ?Transfer $transfer = null;

    public function __construct(Wallet $wallet, int $points, ?Transfer $transfer = null)
    {
        parent::__construct($wallet, $points);
        $this->transfer = $transfer;
        $wallet->addTransaction($this);
    }

    public function getTransfer(): ?Transfer
    {
        return $this->transfer;
    }

    public function getType(): string
    {
        if (null !== $this->transfer) {
            return 'Retrocession';
        }

        if (null !== $this->order) {
            return sprintf('Debit - Command %s', $this->order->getReference());
        }

        return 'Debit';
    }
}
