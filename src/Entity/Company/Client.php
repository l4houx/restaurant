<?php

declare(strict_types=1);

namespace App\Entity\Company;

use App\Entity\Address;
use App\Entity\User\Customer;
use App\Entity\User\SalesPerson;
use App\Repository\Company\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends Company
{
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private ?Address $address = null;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?Member $member = null;

    /**
     * @var Collection<int, Customer>
     */
    #[ORM\OneToMany(targetEntity: Customer::class, mappedBy: 'client')]
    private Collection $customers;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?SalesPerson $salesPerson = null;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
        $this->address = new Address();
    }

    public static function getType(): string
    {
        return 'Client';
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

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    /*
    public function addCustomer(Customer $customer): static
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->setClient($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): static
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getClient() === $this) {
                $customer->setClient(null);
            }
        }

        return $this;
    }
    */

    public function getSalesPerson(): ?SalesPerson
    {
        return $this->salesPerson;
    }

    public function setSalesPerson(?SalesPerson $salesPerson): static
    {
        $this->salesPerson = $salesPerson;

        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if (null !== $this->salesPerson and $this->salesPerson->getMember() !== $this->member) {
            $context->buildViolation('The attached salesperson does not belong to the selected member.')
                ->atPath('salesPerson')
                ->addViolation()
            ;
        }
    }
}
