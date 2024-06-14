<?php

namespace App\Security\Voter;

use App\Entity\User\Customer;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CustomerVoter extends Voter
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
        ]) && $subject instanceof Customer;
    }

    /**
     * @param Customer $customer
     */
    protected function voteOnAttribute(string $attribute, $customer, TokenInterface $token): bool
    {
        /** @var SalesPerson|Manager $employee */
        $employee = $token->getUser();

        if (!$this->canByRoles($customer, $employee)) {
            return false;
        }

        if (in_array($attribute, [self::ROLE_RESET, self::ROLE_LOG_AS, self::ROLE_EDIT])) {
            return true;
        }

        if (self::ROLE_ACTIVE === $attribute) {
            return $this->canOnActive($customer, $employee);
        }

        if (self::ROLE_SUSPEND === $attribute) {
            return $this->canOnSuspend($customer, $employee);
        }

        return $employee instanceof Manager;
    }

    private function canByRoles(Customer $customer, SalesPerson|Manager $employee): bool
    {
        if ($employee instanceof Manager) {
            return $employee->getMembers()->contains($customer->getClient()->getMember());
        }

        return $customer->getClient()->getSalesPerson() === $employee;
    }

    private function canOnActive(Customer $customer, SalesPerson|Manager $employee): bool
    {
        return $customer->isSuspended() && $employee instanceof Manager;
    }

    private function canOnSuspend(Customer $customer, SalesPerson|Manager $employee): bool
    {
        return !$customer->isSuspended() && $employee instanceof Manager;
    }
}
