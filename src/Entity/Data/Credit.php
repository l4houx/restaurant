<?php

namespace App\Entity\Data;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Credit extends Transaction
{
    public const OPERATION = 'CREDIT';

    #[ORM\OneToOne]
    private ?Transfer $transfer = null;

    public function __construct(Wallet $wallet, int $points, Transfer $transfer)
    {
        parent::__construct($wallet, $points);
        $this->transfer = $transfer;
        $this->wallet->addTransaction($this);
    }

    public function getType(): string
    {
        return 'Credit';
    }

    public function getTransfer(): Transfer
    {
        return $this->transfer;
    }
}
