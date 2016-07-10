<?php

namespace Hackzilla\Bundle\TicketBundle\Entity;

use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Entity\Traits\Ticket as TicketTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ticket.
 */
class TicketWithAttachment implements TicketInterface
{
    use TicketTrait;
}
