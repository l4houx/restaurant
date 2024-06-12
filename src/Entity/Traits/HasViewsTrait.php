<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Meilisearch\Bundle\Searchable;
use Symfony\Component\Serializer\Attribute\Groups;

trait HasViewsTrait
{
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    //#[Groups(Searchable::NORMALIZATION_GROUP)]
    private ?int $views = 0;

    public function __construct()
    {
        $this->views = 0;
    }

    public function viewed(): void
    {
        ++$this->views;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): static
    {
        $this->views = $views;

        return $this;
    }
}
