<?php

namespace Hackzilla\Bundle\TicketBundle\Component;

use Hackzilla\TicketMessage\Model\TicketFeature\MessageAttachmentInterface;

class TicketFeatures extends \Hackzilla\TicketMessage\Component\TicketFeatures
{
    /**
     * @param array  $features
     * @param string $messageClass TicketMessage class
     */
    public function __construct(array $features, $messageClass)
    {
        parent::__construct($features);
        $attachmentFeature = \Hackzilla\TicketMessage\Component\TicketFeatures::TICKET_ATTACHMENT;

        if (
            is_a($messageClass, MessageAttachmentInterface::class, true) === false
            &&
            $this->hasFeature($attachmentFeature) === true
        ) {
            $this->unsetFeature($attachmentFeature);
        }
    }
}
