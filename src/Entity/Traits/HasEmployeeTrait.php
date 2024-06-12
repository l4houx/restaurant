<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\Company\Member;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
trait HasEmployeeTrait
{
    #[ORM\ManyToOne]
    private ?Member $member = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^0[0-9]{9}$/',
        message: 'This value is not a valid phone number.',
    )]
    private string $phone = '';

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
