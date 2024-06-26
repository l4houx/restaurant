<?php

namespace App\Entity\Tickets;

use App\Entity\Traits\HasContentTrait;
use App\Entity\Traits\HasDeletedAtTrait;
use App\Entity\Traits\HasIdTrait;
use App\Entity\Traits\HasTimestampableTrait;
use App\Entity\User;
use App\Repository\Tickets\ResponseRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ResponseRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Response
{
    use HasIdTrait;
    use HasContentTrait;
    use HasTimestampableTrait;
    use HasDeletedAtTrait;

    #[ORM\ManyToOne(inversedBy: 'responses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __toString(): string
    {
        return $this->getTicket()->getSubject();
    }

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

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
}
