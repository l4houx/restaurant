<?php

declare(strict_types=1);

namespace App\Entity\Company;

use Doctrine\DBAL\Types\Types;
use App\Validator\CompanyNumber;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;
use App\EntityListener\CompanyListener;
use App\Entity\Traits\HasDeletedAtTrait;
use function Symfony\Component\String\u;

use App\Repository\Company\CompanyRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\EntityListeners([CompanyListener::class])]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['member' => Member::class, 'client' => Client::class, 'organization' => Organization::class])]
abstract class Company implements \Stringable
{
    use HasIdTrait;
    use HasDeletedAtTrait;

    #[ORM\Column(type: Types::STRING, length: 128, unique: true)]
    #[Assert\NotBlank(message: "Please don't leave your name blank!")]
    #[Assert\Length(
        min: 1,
        max: 128,
        minMessage: 'The name is too short ({{ limit }} characters minimum)',
        maxMessage: 'The name is too long ({ limit } characters maximum)'
    )]
    private string $name = '';

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $vatNumber = '';

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[CompanyNumber]
    private ?string $companyNumber = null;

    abstract public static function getType(): string;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getName(): ?string
    {
        return u($this->name)->upper()->toString();
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getVatNumber(): string
    {
        return $this->vatNumber;
    }

    public function setVatNumber(string $vatNumber): static
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    public function getCompanyNumber(): ?string
    {
        return $this->companyNumber;
    }

    public function setCompanyNumber(?string $companyNumber): static
    {
        $this->companyNumber = $companyNumber;

        return $this;
    }
}
