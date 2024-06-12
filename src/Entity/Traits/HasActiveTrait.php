<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait HasActiveTrait
{
    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 1])]
    #[Assert\NotNull]
    private int $isActive;

    public function getIsActive(): int
    {
        return $this->isActive;
    }

    public function setIsActive(int $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }
}
