<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Tickets\Ticket;
use App\Entity\Traits\HasRoles;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TicketVoter extends Voter
{
    final public const LIST = 'LIST';
    final public const LIST_ALL = 'LIST_ALL';
    final public const CREATE = 'CREATE';
    final public const SHOW = 'SHOW';
    final public const MANAGE = 'MANAGE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
            in_array($attribute, [self::CREATE, self::LIST, self::LIST_ALL]) ||
            (
                in_array($attribute, [self::SHOW, self::MANAGE])
                && $subject instanceof Ticket
            );
    }

    /**
     * @param Ticket|null $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if($this->security->isGranted(HasRoles::ADMINAPPLICATION)) return true;

        return match ($attribute) {
            self::MANAGE => $this->canManage($user, $subject),
            self::LIST_ALL => $this->canListAll(),
            self::LIST, self::CREATE, self::SHOW => true,
            default => false,
        };
    }

    private function canManage(User $user, Ticket $ticket): bool
    {
        //return $ticket->getUser()->getId() === $ticket->getId();
        return $user->isVerified() && $user == $ticket->getUser()->getId();
    }

    private function canListAll(): bool
    {
        return $this->security->isGranted(HasRoles::ADMINAPPLICATION);
    }
}
