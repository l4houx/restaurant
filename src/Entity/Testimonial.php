<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasRatingTrait;
use App\Entity\Traits\HasContentTrait;
use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdNameSlugTrait;
use App\Repository\TestimonialRepository;
use App\Entity\Traits\HasTimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TestimonialRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
class Testimonial
{
    use HasIdNameSlugTrait;
    use HasRatingTrait;
    use HasContentTrait;
    use HasIsOnlineTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    #[ORM\ManyToOne(inversedBy: 'testimonials')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    public function __toString(): string
    {
        return (string) $this->author->getUsername();
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
}
