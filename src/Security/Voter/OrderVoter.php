<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Order\Order;
use App\Entity\User\Customer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use App\Entity\User\SalesPerson;

class OrderVoter extends Voter
{
    public const SHOW = 'show';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::SHOW]) && $subject instanceof Order;
    }

    /**
     * @param Order $order
     */
    protected function voteOnAttribute(string $attribute, $order, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return $user === $order->getUser() || (
            $user instanceof SalesPerson
            && $order->getUser() instanceof Customer
            && $user->getClients()->contains($order->getUser()->getClient())
        );
    }
}
