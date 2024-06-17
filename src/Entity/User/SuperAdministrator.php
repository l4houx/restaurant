<?php

namespace App\Entity\User;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Company\Member;
use App\Entity\Traits\HasRoles;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Entity\Traits\HasEmployeeTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\User\SuperAdministratorRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class SuperAdministrator extends User
{
    use HasEmployeeTrait;

    /**
     * @var Collection<int, Member>
     */
    #[ORM\ManyToMany(targetEntity: Member::class, inversedBy: 'superadministrators')]
    #[ORM\JoinTable(name: 'superadministrator_members')]
    private Collection $members;

    /**
     * @var Collection<int, Post>
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $posts;

    public function __construct()
    {
        parent::__construct();
        $this->members = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getRole(): string
    {
        //return HasRoles::ADMINAPPLICATION;
        return '<span class="badge me-2 bg-danger">Administrator</span>';
    }

    public function getRoleName(): string
    {
        return "Administrator";
    }

    public function getCrossRoleName(): string
    {
        return $this->getFullName();
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function setMember(?Member $member): static
    {
        $this->member = $member;

        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    /*
    public function addMember(Member $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(Member $member): static
    {
        $this->members->removeElement($member);

        return $this;
    }
    */

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
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }
}
