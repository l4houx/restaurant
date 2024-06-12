<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

//#[UniqueEntity('name', message: 'That name already exists.')]
//#[UniqueEntity('slug', message: 'This slug already exists.')]

trait HasIdNameSlugTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 128, unique: true)]
    #[Assert\NotBlank(message: "Please don't leave your name blank!")]
    #[Assert\Length(
        min: 4,
        max: 128,
        minMessage: 'The name is too short ({{ limit }} characters minimum)',
        maxMessage: 'The name is too long ({ limit } characters maximum)'
    )]
    private string $name = '';

    #[ORM\Column(type: Types::STRING, length: 128, unique: true)]
    private string $slug = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
