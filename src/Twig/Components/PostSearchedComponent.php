<?php

namespace App\Twig\Components;

use App\Entity\Post;
use App\Repository\PostRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'post-searched')]
final class PostSearchedComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    /**
     * @return array<Post>
     */
    public function getPosts(): array
    {
        return $this->postRepository->findBySearchQuery($this->query);
    }


    public function getCountPost(): int
    {
        return $this->postRepository->count([]);
    }
}
