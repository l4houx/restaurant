<?php

namespace App\Security\Voter;

use App\Entity\Company\Client;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ClientVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Client;
    }

    /**
     * @param Client $client
     */
    protected function voteOnAttribute(string $attribute, $client, TokenInterface $token): bool
    {
        /** @var SalesPerson|Manager $employee */
        $employee = $token->getUser();

        if (
            $employee instanceof SalesPerson
            || (
                $employee instanceof Manager
                && !$employee->getMembers()->contains($client->getMember())
            )
        ) {
            return false;
        }

        return true;
    }
}
