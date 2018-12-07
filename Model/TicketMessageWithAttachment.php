<?php

namespace Hackzilla\Bundle\TicketBundle\Model;

/**
 * Ticket Message.
 */
abstract class TicketMessageWithAttachment extends TicketMessage implements MessageAttachmentInterface
{
    use MessageAttachmentTrait;

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
