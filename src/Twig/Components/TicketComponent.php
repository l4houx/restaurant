<?php

namespace App\Twig\Components;

use App\Entity\Tickets\Ticket;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ticket', template: 'components/ticket.html.twig')]
final class TicketComponent
{
    public Ticket $ticket;
    public bool $hidden = false;
}
