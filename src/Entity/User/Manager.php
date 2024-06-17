<?php

namespace App\Entity\User;

use App\Entity\Company\Member;
use App\Entity\Traits\HasEmployeeTrait;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class Manager extends User
{
    use HasEmployeeTrait;

    /**
     * @var Collection<int, Member>
     */
    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'managers')]
    #[ORM\JoinTable(name: 'manager_members')]
    private Collection $members;

    public function __construct()
    {
        parent::__construct();
        $this->members = new ArrayCollection();
    }

    public function getRole(): string
    {
        // return HasRoles::MANAGER;
        return '<span class="badge me-2 bg-primary">Manager</span>';
    }

    public function getRoleName(): string
    {
        return 'Manager';
    }

    public function getCrossRoleName(): string
    {
        return $this->getFullName();
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    /*
    public function addMember(Member $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(Member $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }
    */
}
