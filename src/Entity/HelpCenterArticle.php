<?php

namespace App\Entity;

use App\Entity\Traits\HasContentTrait;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdNameSlugTrait;
use App\Entity\Traits\HasIsFeaturedTrait;
use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\Traits\HasMetaTrait;
use App\Entity\Traits\HasTagTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Entity\Traits\HasViewsTrait;
use App\Repository\HelpCenterArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: HelpCenterArticleRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
class HelpCenterArticle
{
    use HasIdNameSlugTrait;
    use HasContentTrait;
    use HasIsOnlineTrait;
    use HasIsFeaturedTrait;
    use HasViewsTrait;
    use HasTagTrait;
    use HasMetaTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HelpCenterCategory $category = null;

    public function getCategory(): ?HelpCenterCategory
    {
        return $this->category;
    }

    public function setCategory(?HelpCenterCategory $category): static
    {
        $this->category = $category;

        return $this;
    }
}
