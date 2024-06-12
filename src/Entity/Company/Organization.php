<?php

declare(strict_types=1);

namespace App\Entity\Company;

use App\Repository\Company\OrganizationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
class Organization extends Company
{
    /**
     * @var Collection<int, Member>
     */
    #[ORM\OneToMany(targetEntity: Member::class, mappedBy: 'organization')]
    private Collection $members;

    public static function getType(): string
    {
        return 'Group';
    }

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /*
    public function addMember(Member $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->setOrganization($this);
        }

        return $this;
    }

    public function removeMember(Member $member): static
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getOrganization() === $this) {
                $member->setOrganization(null);
            }
        }

        return $this;
    }
    */
}
