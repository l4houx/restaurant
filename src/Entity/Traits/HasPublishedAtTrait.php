<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Meilisearch\Bundle\Searchable;
use Symfony\Component\Serializer\Attribute\Groups;

trait HasPublishedAtTrait
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    //#[Groups(Searchable::NORMALIZATION_GROUP)]
    private ?\DateTime $publishedAt = null;

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTime $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function isPublished(): bool
    {
        return null !== $this->getPublishedAt() && $this->getPublishedAt() <= new \DateTime();
    }
}
