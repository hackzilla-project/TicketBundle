<?php

namespace Hackzilla\Bundle\TicketBundle\Entity;

use Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketMessage as TicketMessageTrait;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ticket Message.
 */
class TicketMessage implements TicketMessageInterface
{
    use TicketMessageTrait;
}
