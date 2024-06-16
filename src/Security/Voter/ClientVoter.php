<?php

namespace App\Security\Voter;

use App\Entity\User\Manager;
use App\Entity\Company\Client;
use App\Entity\User\SalesPerson;
use App\Entity\User\SuperAdministrator;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ClientVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Client;
    }

    /**
     * @param Client $client
     */
    protected function voteOnAttribute(string $attribute, $client, TokenInterface $token): bool
    {
        /** @var SalesPerson|Manager|SuperAdministrator $employee */
        $employee = $token->getUser();

        if (
            $employee instanceof SalesPerson
            || (
                $employee instanceof Manager && $employee instanceof SuperAdministrator
                && !$employee->getMembers()->contains($client->getMember())
            )
        ) {
            return false;
        }

        return true;
    }
}
