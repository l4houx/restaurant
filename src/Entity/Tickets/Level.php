<?php

namespace App\Entity\Tickets;

use App\Entity\Traits\HasIdNameColorTrait;
use App\Repository\Tickets\LevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LevelRepository::class)]
class Level
{
    use HasIdNameColorTrait;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'level')]
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
            $ticket->setLevel($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getLevel() === $this) {
                $ticket->setLevel(null);
            }
        }

        return $this;
    }
}
