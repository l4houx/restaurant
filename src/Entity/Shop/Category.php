<?php

namespace App\Entity\Shop;

use App\Entity\Traits\HasIdGedmoNameSlugAssertTrait;
use App\Repository\Shop\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
// #[UniqueEntity('name')]
// #[UniqueEntity('slug')]
#[Gedmo\Tree(type: 'nested')]
class Category
{
    use HasIdGedmoNameSlugAssertTrait;

    #[ORM\Column(name: 'lft', type: Types::INTEGER)]
    #[Gedmo\TreeLeft]
    private int $left;

    #[ORM\Column(name: 'rgt', type: Types::INTEGER)]
    #[Gedmo\TreeRight]
    private int $right;

    #[ORM\Column(name: 'lvl', type: Types::INTEGER)]
    #[Gedmo\TreeLevel]
    private int $level;

    /**
     * @var Collection<int, SubCategory>
     */
    #[ORM\ManyToMany(targetEntity: SubCategory::class, mappedBy: 'categories')]
    private Collection $subCategories;

    #[ORM\ManyToOne]
    #[Gedmo\TreeRoot]
    private ?Category $root = null;

    #[ORM\ManyToOne(inversedBy: 'children')]
    #[Gedmo\TreeParent]
    private ?Category $parent = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'parent')]
    private Collection $children;

    #[ORM\Column(type: Types::INTEGER)]
    private int $numberOfProducts = 0;

    #[ORM\ManyToOne]
    private ?Product $lastProduct = null;

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getLeft(): int
    {
        return $this->left;
    }

    public function setLeft(int $left): static
    {
        $this->left = $left;

        return $this;
    }

    public function getRight(): int
    {
        return $this->right;
    }

    public function setRight(int $right): static
    {
        $this->right = $right;

        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, SubCategory>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function getRoot(): ?Category
    {
        return $this->root;
    }

    public function setRoot(?Category $root): static
    {
        $this->root = $root;

        return $this;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getNumberOfProducts(): int
    {
        return $this->numberOfProducts;
    }

    public function setNumberOfProducts(int $numberOfProducts): static
    {
        $this->numberOfProducts = $numberOfProducts;

        return $this;
    }

    public function getLastProduct(): ?Product
    {
        return $this->lastProduct;
    }

    public function setLastProduct(?Product $lastProduct): static
    {
        $this->lastProduct = $lastProduct;

        return $this;
    }
}
