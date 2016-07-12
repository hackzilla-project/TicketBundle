<?php

namespace Hackzilla\Bundle\TicketBundle\Entity;

use Hackzilla\Bundle\TicketBundle\Model\TicketFeature\MessageAttachmentInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketMessage as TicketMessageTrait;
use Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketMessageAttachment as MessageAttachmentTrait;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Ticket Message.
 *
 * @Vich\Uploadable
 */
class TicketMessageWithAttachment implements TicketMessageInterface, MessageAttachmentInterface
{
    use TicketMessageTrait;
    use MessageAttachmentTrait;
}
