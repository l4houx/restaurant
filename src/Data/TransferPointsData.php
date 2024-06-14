<?php

namespace App\Data;

use App\Entity\Data\Debit;
use App\Entity\Data\Credit;
use App\Entity\Data\Wallet;
use App\Entity\Data\Transfer;
use App\Repository\Data\WalletRepository;

class TransferPointsData implements TransferPointsInterface
{
    public function __construct(private readonly WalletRepository $walletRepository)
    {

    }

    public function execute(Transfer $transfer): void
    {
        $points = $transfer->getPoints();

        /** @var Wallet $remainingWallet */
        foreach ($transfer->getFrom()->getRemainingWallets() as $remainingWallet) {
            $pointsToDebit = $remainingWallet->getBalance() < $points
                ? $remainingWallet->getBalance()
                : $points;
            /** @var Debit|Wallet $debit */
            $debit = new Debit($remainingWallet, -$pointsToDebit, $transfer);

            
            $transfer->getTransactions()->add($debit);

            $wallet = $this->walletRepository->findOneByAccountAndPurchase(
                $transfer->getTo(),
                $remainingWallet->getPurchase()
            );

            if ($wallet === null) {
                $wallet = new Wallet($transfer->getTo(), $debit->getWallet()->getExpiredAt());
                $wallet->setPurchase($remainingWallet->getPurchase());
            }

            /** @var Credit|Wallet $credit */
            $credit = new Credit($wallet, $pointsToDebit, $transfer);

            $transfer->getTransactions()->add($credit);

            $points -= $pointsToDebit;

            if ($points === 0) {
                break;
            }
        }
    }
}