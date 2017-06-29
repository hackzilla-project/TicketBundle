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
        if (!empty($features['attachment']) && !is_a($messageClass, MessageAttachmentInterface::class, true)
        ) {
            $features['attachment'] = false;
        }

        parent::__construct($features);
    }
}
