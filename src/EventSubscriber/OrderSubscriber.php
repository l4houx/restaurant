<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\Data\Debit;
use App\Entity\Data\Wallet;
use App\Entity\Order\Order;
use App\Entity\Data\Transaction;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OrderSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {

    }

    public function onCompletedValidCart(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        $total = $order->getTotal();

        foreach ($order->getUser()->getAccount()->getRemainingWallets() as $remainingWallet) {
            $pointsToDebit = $remainingWallet->getBalance() < $total
                ? $remainingWallet->getBalance()
                : $total;

            /** @var Debit|Wallet|Transaction $debit */
            $debit = new Debit($remainingWallet, -$pointsToDebit, null);
            $debit->setOrder($order);

            $order->getTransactions()->add($debit);

            $total -= $pointsToDebit;

            if ($total === 0) {
                break;
            }
        }
    }

    public function onGuardValidCart(GuardEvent $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user->getAccount()->getBalance() < $order->getTotal()) {
            $event->setBlocked(true);
        }
    }

    /**
     * @codeCoverageIgnore
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            "workflow.order.guard.valid_cart" => "onGuardValidCart",
            "workflow.order.completed.valid_cart" => "onCompletedValidCart",
        ];
    }
}
