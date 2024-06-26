<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasTagTrait;
use App\Repository\PostRepository;
use App\Entity\Traits\HasMetaTrait;
use App\Entity\Traits\HasViewsTrait;
use App\Entity\Traits\HasContentTrait;
use App\Entity\Traits\HasIsOnlineTrait;
use App\Entity\User\SuperAdministrator;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdNameSlugTrait;
use App\Entity\Traits\HasPublishedAtTrait;
use Doctrine\Common\Collections\Collection;
use App\Entity\Traits\HasTimestampableTrait;
use App\Entity\Traits\HasGedmoTimestampTrait;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\Traits\HasIdGedmoNameSlugAssertTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity('name')]
#[UniqueEntity('slug')]
class Post
{
    use HasIdNameSlugTrait;
    //use HasIdGedmoNameSlugAssertTrait;
    use HasContentTrait;
    use HasIsOnlineTrait;
    use HasViewsTrait;
    use HasTagTrait;
    use HasMetaTrait;
    use HasPublishedAtTrait;
    use HasTimestampableTrait;
    //use HasGedmoTimestampTrait;
    use HasDeletedAtTrait;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'post_image', fileNameProperty: 'imageName')]
    #[Assert\File(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'],
        mimeTypesMessage: 'The file should be an image'
    )]
    private ?File $imageFile = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: true)]
    // #[Groups(Searchable::NORMALIZATION_GROUP)]
    private ?string $imageName = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    // #[Groups(Searchable::NORMALIZATION_GROUP)]
    private ?int $readtime = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?PostType $type = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?PostCategory $category = null;

    /**
     * @var collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\OrderBy(['publishedAt' => 'DESC'])]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SuperAdministrator $author = null;

    public function stringifyStatus(): string
    {
        if (!$this->isOnline) {
            return 'Offline';
        } elseif (!$this->isOnline) {
            return 'Draft';
        } elseif (!$this->isOnline) {
            return 'Schedule';
        } else {
            return 'Online';
        }
    }

    public function stringifyStatusClass(): string
    {
        if (!$this->isOnline) {
            return 'danger';
        } elseif (!$this->isOnline) {
            return 'warning';
        } elseif (!$this->isOnline) {
            return 'info';
        } else {
            return 'success';
        }
    }

    public function __toString(): string
    {
        return sprintf('#%d %s', $this->getId(), $this->getName());
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     */
    public function setImageFile(File|UploadedFile|null $imageFile): static
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->setUpdatedAt(new \DateTimeImmutable());
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImagePath(): string
    {
        return 'uploads/post/'.$this->imageName;
    }

    public function getImagePlaceholder(string $size = 'default'): string
    {
        if ('small' == $size) {
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAV4AAAFeCAMAAAD69YcoAAAAyVBMVEUAAADd3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d0jcjdZAAAAQnRSTlMAAQIEBQYLDA8SExQWFxkaHyAhJCUmKi06O0lRVVZfYmNkb3V3g46PkZKbo6ivtLW5vL7FyMrT19na3Ojr7fHz9/luoWfVAAACeUlEQVR42u3cBXKDQBiAUaDukkrqSb1N3d3uf6jeoJbuBtj3nWD3zTDAvwxZJkmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJElSjD5qWxsvXrx48eLFixcvXrx48eLFixcvXrwl571oV7cK8JZiTTXaCl68eG0FrzXhxYsXL168ePHixYsXr62kwXuz/2WbeLvayjdd4sWLFy9evHjx4sWLFy9evHjx4sX7B96ir7zl1efdL/H30Qt48eLFixcvXrx48eLFixcvXrx48eKtF+9Yo7wNOK1wWoEXL168ePHixYsXL168ePHixYsXL94f8/7vOL3RFYjTCrx48eLFixcvXrx48eLFixcvXrx48XbBm/wHqGF5jdPx4sWLFy9evHjx4sWLFy9evHjx4sVrnI4XL168ePHixYsXL168ePHixYsXL168eI3T8eLFixcvXrx48eLFixcvXrx48eLFW07e3/1gYAJvyHF6By9evHjx4sWLFy9evHjx4sWLFy9evPXmHWv8Z/3mvdUNL168ePHixYsXbyV5L/CG5D3CG5L3AG9I3hbekLxNvCF5R/AG5H3N8Abk3YmzprePNJuOw/uQpu59pCuqkyZvMxLvepK6d3kk3pkkeedi3W7zpwR1d+M9zqykp3tbxOMtHlPTfRmO+TS+lJju+2Tct529pHSfJ+LqZsV1QrpXQ9Ff1gdOk9HdyrP45dtp4J6P92jaNHVWf9zj2R6O8+YPa217sjLY43lpMdVc3WjXrtba8uJokUmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmSJEmS1Js+AbSytGbYpVXAAAAAAElFTkSuQmCC';
        } else {
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABgAAAAYACAMAAACHMHNZAAABVlBMVEUAAADd3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d3d18zGJfAAAAcXRSTlMAAQIDBAUHCAkLDA0PEBESExQWFxocHiAjJCUmKywtLzA0NTY3ODk6Pj9AQUJHSU1OT1BYWV9jZGZoaWtsbW90dXd7fH5/goOFjpSXmJqeoKOlpqq1t7nBw8XKzs/T1dfZ3N7g4uTm6+3v8fP19/n7/XM0K4AAABCKSURBVHja7d1bbxR1HMfhBSe1EKkYUEAjkaioCWjwUmO4IF544ZXR97GvZN+FhlTlwqChRoyHiASCLaek0FLLSqEFSkuB3e62XTmESqHF0m6Znfk9zz1Rvo799N/OzBYKAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAECrWxV9gKJrAFJTMkGqVpsAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQCg2RITLE/JBARWNIETAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAA9KTACkpZjyP7/kBACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAEBzJSZIV9EEpKhkAicAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAAmikxQbpKJgCcAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABABAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQAQAAAEAAABAEAAABAAAPIiMUG6iiZYlpL9U90PJwAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEAQAAAEAAABAAAAQBAAAAQAADmSExAZCUT4AQAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgBA8yQmILJi8L9/ySXgBACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACACAAAAgAAAIAAACAIAAACAAAAgAAAIAgAAAIAAACAAAAgCAAAAgAAAIAAACAEAzJSYgspIJcAIAQAAAEAAABAAAAQBAAAAQAAAEAAABAEAAABAAAAQAAAEA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';
        }
    }

    public function getReadtime(): ?int
    {
        return $this->readtime;
    }

    public function setReadtime(?int $readtime): static
    {
        $this->readtime = $readtime;

        return $this;
    }

    public function hasVideo(): bool
    {
        if (null !== $this->getContent()) {
            return 1 === preg_match('/^[^\s]*youtube.com/mi', $this->getContent());
        }

        return false;
    }

    public function getType(): ?PostType
    {
        return $this->type;
    }

    public function setType(?PostType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCategory(): ?PostCategory
    {
        return $this->category;
    }

    public function setCategory(?PostCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?SuperAdministrator
    {
        return $this->author;
    }

    public function setAuthor(?SuperAdministrator $author): static
    {
        $this->author = $author;

        return $this;
    }
}
