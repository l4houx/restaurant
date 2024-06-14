<?php

namespace App\Security\Voter;

use App\Entity\User\Collaborator;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EmployerVoter extends Voter
{
    public const ROLE_SUSPEND = 'SUSPEND';

    public const ROLE_ACTIVE = 'ACTIVE';

    public const ROLE_EDIT = 'EDIT';

    public const ROLE_RESET = 'RESET';

    public const ROLE_DELETE = 'DELETE';

    public const ROLE_LOG_AS = 'LOG_AS';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::ROLE_SUSPEND,
            self::ROLE_ACTIVE,
            self::ROLE_EDIT,
            self::ROLE_RESET,
            self::ROLE_DELETE,
            self::ROLE_LOG_AS,
        ]) && in_array($subject::class, [
            Collaborator::class,
            SalesPerson::class,
            Manager::class,
        ])
        ;
    }

    /**
     * @param Collaborator|SalesPerson|Manager $user
     */
    protected function voteOnAttribute(string $attribute, $user, TokenInterface $token): bool
    {
        /** @var Manager $manager */
        $manager = $token->getUser();

        if ($manager instanceof Manager && !$manager->getMembers()->contains($user->getMember())) {
            return false;
        }

        switch ($attribute) {
            case self::ROLE_ACTIVE:
                return $user->isSuspended()
                ;
            case self::ROLE_SUSPEND:
                return !$user->isSuspended()
                ;
        }

        return true;
    }
}
