<?php

namespace App\Entity;

use App\Entity\Traits\HasContentTrait;
use App\Entity\Traits\HasIdNameSlugTrait;
use App\Entity\Traits\HasMetaTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Entity\Traits\HasViewsTrait;
use App\Repository\PageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
class Page
{
    use HasIdNameSlugTrait;
    use HasContentTrait;
    use HasViewsTrait;
    use HasMetaTrait;
    use HasTimestampableTrait;

    public function __toString(): string
    {
        return (string) $this->getName() ?: '';
    }
}
