<?php

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Component;

use Hackzilla\Bundle\TicketBundle\Model\TicketFeature\MessageAttachmentInterface;

/**
 * @final since hackzilla/ticket-bundle 3.x.
 */
class TicketFeatures
{
    /**
     * @var array<string, bool>
     */
    private $features = [];

    /**
     * @param array<string, bool> $features
     * @param string              $messageClass TicketMessage class
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
     * NEXT_MAJOR: Remove the BC checks and return only boolean values.
     *
     * Check if feature exists or whether enabled.
     *
     * @param string $feature
     *
     * @return bool|null
     */
    public function hasFeature($feature)
    {
        $args = \func_get_args();
        if (isset($args[1]) && 'return_strict_bool' === $args[1]) {
            return isset($this->features[$feature]) && $this->features[$feature];
        }

        if (!isset($this->features[$feature])) {
            @trigger_error(sprintf(
                'Returning other type than boolean from "%s()" is deprecated since hackzilla/ticket-bundle 3.x'
                .' and will be not allowed in version 4.0.',
                __METHOD__
            ), E_USER_DEPRECATED);

            return null;
        }

        return (bool) $this->features[$feature];
    }
}
