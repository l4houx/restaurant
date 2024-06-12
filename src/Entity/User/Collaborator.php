<?php

declare(strict_types=1);

namespace App\Entity\User;

use App\Entity\Company\Member;
use App\Entity\Traits\HasEmployeeTrait;
use App\Entity\User;
use App\Repository\User\CollaboratorRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AssociationOverride;

#[ORM\Entity(repositoryClass: CollaboratorRepository::class)]
#[ORM\AssociationOverrides([
    new AssociationOverride(
        name: 'member',
        inversedBy: 'collaborators'
    ),
])]
class Collaborator extends User
{
    use HasEmployeeTrait;

    /*
    #[ORM\ManyToOne(inversedBy: 'collaborators')]
    private ?Member $member = null;
    */

    public function getRole(): string
    {
        return '<span class="badge me-2 bg-secondary bg-opacity-15 text-secondary">Collabarator</span>';
    }

    public function getCrossRoleName(): string
    {
        return $this->getFullName();
    }

    /*
    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }
    */
}
