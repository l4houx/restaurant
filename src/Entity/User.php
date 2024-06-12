<?php

namespace App\Entity;

use App\Entity\Data\Account;
use App\Entity\User\Manager;
use App\Entity\User\Customer;
use Doctrine\DBAL\Types\Types;
use App\Entity\Traits\HasRoles;
use App\Entity\User\SalesPerson;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\HasIdTrait;
use App\Entity\User\Collaborator;
use App\Repository\UserRepository;
use App\Entity\Traits\HasIsTeamTrait;
use App\Entity\User\SuperAdministrator;

use App\Entity\Traits\HasDeletedAtTrait;
use function Symfony\Component\String\u;
use Doctrine\Common\Collections\Collection;
use App\Entity\Traits\HasTimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Traits\HasRegistrationDetailsTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['superadministrator' => SuperAdministrator::class, 'manager' => Manager::class, 'customer' => Customer::class, 'collaborator' => Collaborator::class, 'sales_person' => SalesPerson::class])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    use HasIdTrait;
    use HasRegistrationDetailsTrait;
    use HasIsTeamTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    #[Assert\Length(min: 2, max: 20)]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $firstname = '';

    #[Assert\Length(min: 2, max: 20)]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $lastname = '';

    #[ORM\Column(type: Types::STRING, length: 30, unique: true)]
    #[Assert\NotBlank(message: "Please don't leave your username blank!")]
    #[Assert\Length(
        min: 4,
        max: 30,
        minMessage: 'The username is too short ({{ limit }} characters minimum)',
        maxMessage: 'The username is too long ({ limit } characters maximum)'
    )]
    private string $username = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\NotNull]
    #[Assert\Length(min: 5, max: 180)]
    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private string $email = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $avatar = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [HasRoles::DEFAULT];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: Types::STRING)]
    private ?string $password = null;

    /**
     * //@var collection<int, Product>
     */
    //#[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'addedtofavoritesby', fetch: 'LAZY', cascade: ['remove'])]
    //private Collection $favorites;

    /**
     * @var collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'author', orphanRemoval: true, cascade: ['remove'])]
    private Collection $reviews;

    /**
     * @var Collection<int, Testimonial>
     */
    #[ORM\OneToMany(targetEntity: Testimonial::class, mappedBy: 'author', orphanRemoval: true, cascade: ['remove'])]
    private Collection $testimonials;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist'], fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    public function __construct()
    {
        $this->isVerified = false;
        //$this->favorites = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->testimonials = new ArrayCollection();
        $this->account = new Account();
    }

    public function __toString(): string
    {
        return (string) $this->getFullName();
    }

    public function getFullName(): string
    {
        return u(sprintf('%s %s', $this->firstname, $this->lastname))->upper()->toString();
    }

    public function getFirstname(): ?string
    {
        return u($this->firstname)->upper()->toString();
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return u($this->lastname)->upper()->toString();
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(?string $username): static
    {
        $this->username = trim($username ?: '');

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email ?: '';

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(HasRoles::SUPERADMIN);
    }

    public function setSuperAdmin($boolean): static
    {
        if (true === $boolean) {
            $this->addRole(HasRoles::SUPERADMIN);
        } else {
            $this->removeRole(HasRoles::SUPERADMIN);
        }

        return $this;
    }

    public function removeRole(string $role): static
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function getRole(): string
    {
        if ($this->hasRole(HasRoles::SUPERADMIN) || $this->hasRole(HasRoles::ADMINAPPLICATION)) {
            return '<span class="badge me-2 bg-danger">Administrator</span>';
        } elseif ($this->hasRole(HasRoles::ADMIN)) {
            return '<span class="badge me-2 bg-dark">Admin</span>';
        } elseif ($this->hasRole(HasRoles::MODERATOR)) {
            return '<span class="badge me-2 bg-warning">Moderator</span>';
        } elseif ($this->hasRole(HasRoles::TEAM)) {
            return '<span class="badge me-2 bg-primary">Team</span>';
        } elseif ($this->hasRole(HasRoles::EDITOR)) {
            return '<span class="badge me-2 bg-secondary">Editor</span>';
        } elseif ($this->hasRole(HasRoles::DEFAULT)) {
            return '<span class="badge me-2 bg-success">User</span>';
        } else {
            return '<span class="badge me-2 bg-warning">N/A</span>';
        }
    }

    public function getCrossRoleName(): string
    {
        if ($this->hasRole(HasRoles::ADMIN)) {
            return $this->getFullName();
        } elseif ($this->hasRole(HasRoles::MODERATOR)) {
            return $this->getFullName();
        } elseif ($this->hasRole(HasRoles::TEAM)) {
            return $this->getFullName();
        } elseif ($this->hasRole(HasRoles::EDITOR)) {
            return $this->getFullName();
        } elseif ($this->hasRole(HasRoles::DEFAULT)) {
            return $this->getFullName();
        } else {
            return 'N/A';
        }
    }

    public function addRole(string $role): static
    {
        $role = strtoupper($role);
        if (HasRoles::DEFAULT === $role) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = HasRoles::DEFAULT;

        if ($this->isVerified) {
            $roles[] = HasRoles::VERIFIED;
        }

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        // $this->roles = $roles;

        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->username,
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->password,
        ];
    }

    public function __unserialize(array $data): void
    {
        if (count($data) === 6) {
            [
                $this->id,
                $this->username,
                $this->firstname,
                $this->lastname,
                $this->email,
                $this->password,
            ] = $data;
        }
    }

    /**
     * //@return Collection<int, Product>
     */
    /*
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Product $favorite): static
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->addAddedtofavoritesby($this);
        }

        return $this;
    }

    public function removeFavorite(Product $favorite): static
    {
        if ($this->favorites->removeElement($favorite)) {
            $favorite->removeAddedtofavoritesby($this);
        }

        return $this;
    }
    */

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
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setAuthor($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getAuthor() === $this) {
                $review->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Testimonial>
     */
    public function getTestimonials(): Collection
    {
        return $this->testimonials;
    }

    public function addTestimonial(Testimonial $testimonial): static
    {
        if (!$this->testimonials->contains($testimonial)) {
            $this->testimonials->add($testimonial);
            $testimonial->setAuthor($this);
        }

        return $this;
    }

    public function removeTestimonial(Testimonial $testimonial): static
    {
        if ($this->testimonials->removeElement($testimonial)) {
            // set the owning side to null (unless already changed)
            if ($testimonial->getAuthor() === $this) {
                $testimonial->setAuthor(null);
            }
        }

        return $this;
    }

    public function isRatedBy(User $user)
    {
        /** @var Testimonial $testimonial */
        foreach ($this->testimonials as $testimonial) {
            if ($testimonial->getAuthor() === $user) {
                return $testimonial;
            }
        }

        return false;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    /*
    public function setAccount(Account $account): static
    {
        $this->account = $account;

        return $this;
    }
    */
}
