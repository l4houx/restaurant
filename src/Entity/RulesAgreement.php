<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;

#[ORM\Entity]
class RulesAgreement
{
    use HasIdTrait;

    #[ORM\ManyToOne(inversedBy: 'rulesAgreements')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Rules $rules;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => 0])]
    private bool $accepted = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $agreedAt;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRules(): Rules
    {
        return $this->rules;
    }

    public function setRules(Rules $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): static
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getAgreedAt(): \DateTimeImmutable
    {
        return $this->agreedAt;
    }

    public function setAgreedAt(\DateTimeImmutable $agreedAt): static
    {
        $this->agreedAt = $agreedAt;

        return $this;
    }
}
