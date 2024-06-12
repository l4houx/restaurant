<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

trait HasBackgroundColorTrait
{
    #[ORM\Column(type: Types::STRING, length: 12, nullable: true, options: ['default' => 'dark'])]
    #[Assert\NotBlank(message: "Please don't leave your color blank!")]
    #[Assert\Length(
        min: 4,
        max: 12,
        minMessage: 'The color is too short ({{ limit }} characters minimum)',
        maxMessage: 'The color is too long ({ limit } characters maximum)'
    )]
    private ?string $color = null;

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }
}
