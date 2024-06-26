<?php

namespace App\Entity\Tickets;

use App\Entity\Traits\HasIdNameColorTrait;
use App\Entity\Traits\HasIsCloseTrait;
use App\Repository\Tickets\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    use HasIdNameColorTrait;
    use HasIsCloseTrait;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'status')]
    private Collection $tickets;

    public function __toString(): string
    {
        return $this->getName();
    }

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setStatus($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getStatus() === $this) {
                $ticket->setStatus(null);
            }
        }

        return $this;
    }
}
