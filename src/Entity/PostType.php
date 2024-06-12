<?php

namespace App\Entity;

use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdNameSlugTrait;
use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Repository\PostTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PostTypeRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
class PostType
{
    use HasIdNameSlugTrait;
    use HasIsOnlineTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    /**
     * @var collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'type')]
    private Collection $posts;

    public function __toString(): string
    {
        return sprintf('#%d %s', $this->getId(), $this->getName());
    }

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setType($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getType() === $this) {
                $post->setType(null);
            }
        }

        return $this;
    }
}
