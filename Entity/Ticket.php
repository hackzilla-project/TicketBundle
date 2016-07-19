<?php

namespace Hackzilla\Bundle\TicketBundle\Entity;

use Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketTrait;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;

/**
 * Ticket.
 */
class Ticket implements TicketInterface
{
    use TicketTrait;
}
