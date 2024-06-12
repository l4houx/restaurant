<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Testimonial;
use App\Entity\Traits\HasRoles;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TestimonialVoter extends Voter
{
    final public const CREATE = 'create';
    final public const EDIT = 'edit';
    final public const MANAGE = 'manage';
    final public const DELETE = 'delete';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return
            in_array($attribute, [self::CREATE])
            || (
                in_array($attribute, [self::MANAGE, self::EDIT, self::DELETE])
                && $subject instanceof Testimonial
            )
        ;
    }

    /**
     * @param Testimonial|null $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(HasRoles::ADMINAPPLICATION)) {
            return true;
        }

        return match ($attribute) {
            self::MANAGE => $this->canManage($user, $subject),
            self::EDIT => $this->canEdit(),
            self::DELETE => $this->canDelete(),
            self::CREATE, => true,
            default => false,
        };
    }

    private function canManage(User $user, Testimonial $testimonial): bool
    {
        //return $testimonial->getAuthor()->getId() === $user->getId();
        return $user->isVerified() && $user == $testimonial->getAuthor();
    }

    private function canEdit(): bool
    {
        return $this->security->isGranted(HasRoles::EDITOR);
    }

    private function canDelete(): bool
    {
        return $this->security->isGranted(HasRoles::ADMINAPPLICATION);
    }
}
