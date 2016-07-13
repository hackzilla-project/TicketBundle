<?php

namespace Hackzilla\Bundle\TicketBundle\Entity;

use Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketMessage as TicketMessageTrait;
use Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketMessageAttachment as MessageAttachmentTrait;
use Hackzilla\Bundle\TicketBundle\Model\TicketFeature\MessageAttachmentInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;

/**
 * Ticket Message.
 */
class TicketMessageWithAttachment implements TicketMessageInterface, MessageAttachmentInterface
{
    use TicketMessageTrait;
    use MessageAttachmentTrait;
}
