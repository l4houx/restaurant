<?php

declare(strict_types=1);

namespace App\Entity\User;

use App\Entity\Company\Client;
use App\Entity\Company\Member;
use App\Entity\Traits\HasEmployeeTrait;
use App\Entity\User;
use App\Repository\User\SalesPersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AssociationOverride;

#[ORM\Entity(repositoryClass: SalesPersonRepository::class)]
#[ORM\AssociationOverrides([
    new AssociationOverride(
        name: 'member',
        inversedBy: 'salesPersons'
    ),
])]
class SalesPerson extends User
{
    use HasEmployeeTrait;

    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'salesPerson')]
    private Collection $clients;

    //#[ORM\ManyToOne(inversedBy: 'salesPersons')]
    //private ?Member $member = null;

    public function __construct()
    {
        parent::__construct();
        $this->clients = new ArrayCollection();
    }

    public function getRole(): string
    {
        return 'Sales';
    }

    public function getCrossRoleName(): string
    {
        return $this->getFullName();
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    /*
    public function addClient(Client $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setSalesPerson($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getSalesPerson() === $this) {
                $client->setSalesPerson(null);
            }
        }

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
    */
}
