<?php

declare(strict_types=1);

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Component;

use Hackzilla\Bundle\TicketBundle\Model\MessageAttachmentInterface;

final class TicketFeatures
{
    /**
     * @param array<string, bool> $features
     * @param string              $messageClass TicketMessage class
     */
    private array $features;

    /**
     * @param string $messageClass TicketMessage class
     */
    public function __construct(array $features, string $messageClass)
    {
        if (!empty($features['attachment']) && !is_a($messageClass, MessageAttachmentInterface::class, true)
        ) {
            $features['attachment'] = false;
        }

        $this->features = $features;
    }

    /**
     * Check if feature exists or whether enabled.
     */
    public function hasFeature(string $feature): bool
    {
        if (!isset($this->features[$feature])) {
            return false;
        }

        return (bool) $this->features[$feature];
    }
}
