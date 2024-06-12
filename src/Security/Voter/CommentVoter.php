<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CommentVoter extends Voter
{
    public const SHOW = 'show';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // this voter is only executed on Comment objects and for three specific permissions
        return $subject instanceof Comment && \in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }

    /**
     * @param Comment $comment
     */
    protected function voteOnAttribute(string $attribute, $comment, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // the user must be logged in; if not, deny permission
        if (!$user instanceof User) {
            return false;
        }

        // the logic of this voter is pretty simple: if the logged-in user is the
        // author of the given comment, grant permission; otherwise, deny it.
        // (the supports() method guarantees that $comment is a Comment object)
        return $user === $comment->getAuthor();
    }
}
