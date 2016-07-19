<?php

namespace Hackzilla\Bundle\TicketBundle\Entity;

use Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketMessageTrait;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;

/**
 * Ticket Message.
 */
class TicketMessage implements TicketMessageInterface
{
    use TicketMessageTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
