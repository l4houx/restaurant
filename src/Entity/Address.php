<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;
use App\Repository\AddressRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    use HasIdTrait;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $streetAddress = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $restAddress = null;

    #[ORM\Column(type: Types::STRING, length: 15)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 15)]
    #[Assert\Regex(
        pattern: '/^[A-Za-z0-9]{2}\d{3}$/',
        message: 'Invalid postal code.',
        groups: ['order']
    )]
    private string $zipCode = '';

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $locality = '';

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    #[Assert\Length(min: 1, max: 50)]
    #[Assert\Regex(
        pattern: '/^0\d{9}$/',
        message: 'Invalid phone number.',
        groups: ['order']
    )]
    private ?string $phone = null;

    #[ORM\Column(type: Types::STRING, length: 180, nullable: true)]
    #[Assert\Email(groups: ['order'])]
    #[Assert\Length(min: 5, max: 180)]
    private ?string $email = null;

    public function getStreetAddress(): string
    {
        return $this->streetAddress;
    }

    public function setStreetAddress(string $streetAddress): static
    {
        $this->streetAddress = $streetAddress;

        return $this;
    }

    public function getRestAddress(): ?string
    {
        return $this->restAddress;
    }

    public function setRestAddress(?string $restAddress): static
    {
        $this->restAddress = $restAddress;

        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getLocality(): string
    {
        return $this->locality;
    }

    public function setLocality(string $locality): static
    {
        $this->locality = $locality;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
