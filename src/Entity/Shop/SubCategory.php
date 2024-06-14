<?php

namespace App\Entity\Shop;

use App\Entity\Traits\HasBackgroundColorTrait;
use App\Entity\Traits\HasIdGedmoNameSlugAssertTrait;
use App\Repository\Shop\SubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SubCategoryRepository::class)]
// #[UniqueEntity('name')]
// #[UniqueEntity('slug')]
class SubCategory
{
    use HasIdGedmoNameSlugAssertTrait;
    use HasBackgroundColorTrait;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'subCategories')]
    #[ORM\JoinTable(name: 'subCategory_categories')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function getNumberOfProducts(): int
    {
        return array_sum(
            $this->categories->map(fn (Category $category) => $category->getNumberOfProducts())->toArray()
        );
    }
}
