<?php

namespace Hackzilla\Bundle\TicketBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Hackzilla\Bundle\TicketBundle\Entity\Ticket;

class TicketEvent extends Event
{
    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function getTicket()
    {
        return $this->ticket;
    }
}
