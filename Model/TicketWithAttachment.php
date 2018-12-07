<?php

namespace Hackzilla\Bundle\TicketBundle\Model;

use Hackzilla\Bundle\TicketBundle\Model\TicketFeature\TicketTrait;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;

/**
 * Ticket.
 */
abstract class TicketWithAttachment implements TicketInterface
{
    use TicketTrait;

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
