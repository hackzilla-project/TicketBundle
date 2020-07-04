<?php

namespace Hackzilla\Bundle\TicketBundle\Event;

use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @final since hackzilla/ticket-bundle 3.x.
 */
class TicketEvent extends Event
{
    protected $ticket;

    public function __construct(TicketInterface $ticket)
    {
        $this->ticket = $ticket;
    }

    public function getTicket()
    {
        return $this->ticket;
    }
}
