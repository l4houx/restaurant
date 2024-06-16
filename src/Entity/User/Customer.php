<?php

declare(strict_types=1);

namespace App\Entity\User;

use App\Entity\Company\Client;
use App\Entity\Traits\HasRoles;
use App\Entity\User;
use App\Repository\User\CustomerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    #[Assert\NotNull]
    private bool $isManualDelivery = false;

    #[ORM\ManyToOne(inversedBy: 'customers')]
    private ?Client $client = null;

    public function getRole(): string
    {
        //return HasRoles::CUSTOMER;
        return '<span class="badge me-2 bg-success">Customer</span>';
    }

    public function getRoleName(): string
    {
        return "Customer";
    }

    public function getCrossRoleName(): string
    {
        return $this->getFullName();
    }

    public function isManualDelivery(): bool
    {
        return $this->isManualDelivery;
    }

    public function getIsManualDelivery(): bool
    {
        return $this->isManualDelivery;
    }

    public function setManualDelivery(bool $isManualDelivery): static
    {
        $this->isManualDelivery = $isManualDelivery;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }
}
