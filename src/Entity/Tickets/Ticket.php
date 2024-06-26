<?php

namespace App\Entity\Tickets;

use App\Entity\Traits\HasContentTrait;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Entity\User;
use App\Repository\Tickets\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Ticket
{
    use HasIdTrait;
    use HasContentTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    #[ORM\Column(type: Types::STRING, length: 128)]
    #[Assert\NotBlank(message: "Please don't leave your subject blank!")]
    #[Assert\Length(
        min: 4,
        max: 128,
        minMessage: 'The subject is too short ({{ limit }} characters minimum)',
        maxMessage: 'The subject is too long ({ limit } characters maximum)'
    )]
    private string $subject = '';

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Level $level = null;

    /**
     * @var Collection<int, Response>
     */
    #[ORM\OneToMany(targetEntity: Response::class, mappedBy: 'ticket', orphanRemoval: true)]
    private Collection $responses;

    public function __toString(): string
    {
        return sprintf('Ticket #%d %s', $this->getId(), $this->getSubject());
    }

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->responses = new ArrayCollection();
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, Response>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Response $response): static
    {
        if (!$this->responses->contains($response)) {
            $this->responses->add($response);
            $response->setTicket($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): static
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getTicket() === $this) {
                $response->setTicket(null);
            }
        }

        return $this;
    }
}
