<?php

namespace App\Entity\Company;

use App\Entity\Address;
use App\Entity\Data\Account;
use App\Entity\User\Manager;
use App\Entity\User\SalesPerson;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User\Collaborator;
use App\Entity\User\SuperAdministrator;
use Doctrine\Common\Collections\Collection;
use App\Repository\Company\MemberRepository;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\Table(name: '`member`')]
class Member extends Company
{
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'member')]
    private Collection $clients;

    #[ORM\ManyToOne(inversedBy: 'members')]
    private ?Organization $organization = null;

    /**
     * @var Collection<int, Manager>
     */
    #[ORM\ManyToMany(targetEntity: Manager::class, mappedBy: 'members')]
    private Collection $managers;

    /**
     * @var Collection<int, Collaborator>
     */
    #[ORM\OneToMany(targetEntity: Collaborator::class, mappedBy: 'member')]
    private Collection $collaborators;

    /**
     * @var Collection<int, SalesPerson>
     */
    #[ORM\OneToMany(targetEntity: SalesPerson::class, mappedBy: 'member')]
    private Collection $salesPersons;

    #[ORM\OneToOne(inversedBy: 'member', cascade: ['persist'], fetch: 'EAGER')]
    private ?Account $account = null;

    /**
     * @var Collection<int, SuperAdministrator>
     */
    #[ORM\ManyToMany(targetEntity: SuperAdministrator::class, mappedBy: 'members')]
    private Collection $superadministrators;

    public static function getType(): string
    {
        return 'Member';
    }

    public function __construct()
    {
        $this->address = new Address();
        $this->clients = new ArrayCollection();
        $this->managers = new ArrayCollection();
        $this->collaborators = new ArrayCollection();
        $this->salesPersons = new ArrayCollection();
        $this->account = new Account();
        $this->superadministrators = new ArrayCollection();
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
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
            $client->setMember($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getMember() === $this) {
                $client->setMember(null);
            }
        }

        return $this;
    }
    */

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return Collection<int, Manager>
     */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    /*
    public function addManager(Manager $manager): static
    {
        if (!$this->managers->contains($manager)) {
            $this->managers->add($manager);
            $manager->addMember($this);
        }

        return $this;
    }

    public function removeManager(Manager $manager): static
    {
        if ($this->managers->removeElement($manager)) {
            $manager->removeMember($this);
        }

        return $this;
    }
    */

    /**
     * @return Collection<int, Collaborator>
     */
    public function getCollaborators(): Collection
    {
        return $this->collaborators;
    }

    /*
    public function addCollaborator(Collaborator $collaborator): static
    {
        if (!$this->collaborators->contains($collaborator)) {
            $this->collaborators->add($collaborator);
            $collaborator->setMember($this);
        }

        return $this;
    }

    public function removeCollaborator(Collaborator $collaborator): static
    {
        if ($this->collaborators->removeElement($collaborator)) {
            // set the owning side to null (unless already changed)
            if ($collaborator->getMember() === $this) {
                $collaborator->setMember(null);
            }
        }

        return $this;
    }
    */

    /**
     * @return Collection<int, SalesPerson>
     */
    public function getSalesPersons(): Collection
    {
        return $this->salesPersons;
    }

    /*
    public function addSalesPerson(SalesPerson $salesPerson): static
    {
        if (!$this->salesPersons->contains($salesPerson)) {
            $this->salesPersons->add($salesPerson);
            $salesPerson->setMember($this);
        }

        return $this;
    }

    public function removeSalesPerson(SalesPerson $salesPerson): static
    {
        if ($this->salesPersons->removeElement($salesPerson)) {
            // set the owning side to null (unless already changed)
            if ($salesPerson->getMember() === $this) {
                $salesPerson->setMember(null);
            }
        }

        return $this;
    }
    */

    public function getAccount(): Account
    {
        return $this->account;
    }

    /*
    public function setAccount(Account $account): static
    {
        $this->account = $account;

        return $this;
    }
    */

    /**
     * @return Collection<int, SuperAdministrator>
     */
    public function getSuperAdministrators(): Collection
    {
        return $this->superadministrators;
    }

    /*
    public function addSuperAdministrator(SuperAdministrator $superadministrator): static
    {
        if (!$this->superadministrators->contains($superadministrator)) {
            $this->superadministrators->add($superadministrator);
            $superadministrator->addMember($this);
        }

        return $this;
    }

    public function removeSuperAdministrator(SuperAdministrator $superadministrator): static
    {
        if ($this->superadministrators->removeElement($superadministrator)) {
            $superadministrator->removeMember($this);
        }

        return $this;
    }
    */
}
