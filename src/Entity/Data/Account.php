<?php

declare(strict_types=1);

namespace App\Entity\Data;

use App\Entity\Company\Member;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Entity\User;
use App\Repository\Data\AccountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
//#[UniqueEntity('reference')]
class Account implements \Stringable
{
    use HasIdTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    #[ORM\OneToOne(mappedBy: 'account')]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'account')]
    private ?Member $member = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        if (null !== $this->user) {
            return sprintf('User : %s', $this->user->getFullName());
        }

        return sprintf('Company : %s', $this->member->getName());
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getOwner(): User|Member
    {
        return $this->user ?? $this->member;
    }
}
