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
     * @param Testimonial|null $testimonial
     */
    protected function voteOnAttribute(string $attribute, $testimonial, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(HasRoles::ADMINAPPLICATION)) {
            return true;
        }

        return match ($attribute) {
            self::MANAGE => $this->canManage($user, $testimonial),
            self::EDIT => $this->canEdit(),
            self::DELETE => $this->canDelete(),
            self::CREATE, => $this->canCreate($user),
            default => false,
        };
    }

    private function canCreate(User $user): bool
    {
        return $user->isVerified() && true;
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
