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

namespace Hackzilla\Bundle\TicketBundle\Form\DataTransformer;

use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Symfony\Component\Form\DataTransformerInterface;

final class StatusTransformer implements DataTransformerInterface
{
    public function __construct(private readonly TicketInterface $ticket)
    {
    }

    /**
     * Transforms checkbox value into Ticket Message Status Closed.
     *
     * @param $value
     *
     * @return bool|null
     */
    public function transform($value): ?bool
    {
        if (TicketMessageInterface::STATUS_CLOSED === $value) {
            return true;
        }

        return null;
    }

    /**
     * Transforms Ticket Message Status Closed into checkbox value checked.
     *
     * @param $value
     *
     * @return int|null
     */
    public function reverseTransform($value): ?int
    {
        if ($value) {
            return TicketMessageInterface::STATUS_CLOSED;
        }

        return $this->ticket->getStatus();
    }
}
