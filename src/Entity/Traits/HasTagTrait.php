<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Meilisearch\Bundle\Searchable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait HasTagTrait
{
    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    #[Assert\Length(max: 500)]
    //#[Groups(Searchable::NORMALIZATION_GROUP)]
    private ?string $tags = null;

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): static
    {
        $this->tags = $tags;

        return $this;
    }
}
