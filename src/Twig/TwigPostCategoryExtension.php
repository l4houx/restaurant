<?php

namespace App\Twig;

use App\Repository\PostCategoryRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPostCategoryExtension extends AbstractExtension
{
    public function __construct(private readonly PostCategoryRepository $postCategoryRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('categories', $this->categories(...)),
        ];
    }

    public function categories(): array
    {
        return $this->postCategoryRepository->findAll();
    }
}
