<?php

namespace Hackzilla\Bundle\TicketBundle\Component;

use Hackzilla\Bundle\TicketBundle\Model\TicketFeature\MessageAttachmentInterface;

/**
 * @final since hackzilla/ticket-bundle 3.x.
 */
class TicketFeatures
{
    private $features;

    /**
     * @param string $messageClass TicketMessage class
     */
    public function __construct(array $features, $messageClass)
    {
        if (!empty($features['attachment']) && !is_a($messageClass, MessageAttachmentInterface::class, true)
        ) {
            $features['attachment'] = false;
        }

        $this->features = $features;
    }

    /**
     * Check if feature exists or whether enabled.
     *
     * @param $feature
     *
     * @return bool|null
     */
    public function hasFeature($feature)
    {
        if (!isset($this->features[$feature])) {
            return null;
        }

        return $this->features[$feature];
    }
}
