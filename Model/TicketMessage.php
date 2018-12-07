<?php

namespace Hackzilla\Bundle\TicketBundle\Model;

use Hackzilla\Bundle\TicketBundle\Model\TicketFeature\TicketMessageTrait;

/**
 * Ticket Message.
 */
abstract class TicketMessage implements TicketMessageInterface
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
