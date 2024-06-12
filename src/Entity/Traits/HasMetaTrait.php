<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait HasMetaTrait
{
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $metaDescription = null;

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): static
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): static
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }
}
