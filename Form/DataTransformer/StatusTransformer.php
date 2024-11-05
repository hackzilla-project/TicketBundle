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
     * @param int $number
     *
     * @return true|null
     */
    public function transform($number): mixed
    {
        if (TicketMessageInterface::STATUS_CLOSED === $number) {
            return true;
        }

        return null;
    }

    /**
     * Transforms Ticket Message Status Closed into checkbox value checked.
     *
     * @param bool $number
     *
     * @return int|null
     */
    public function reverseTransform($number): mixed
    {
        if ($number) {
            return TicketMessageInterface::STATUS_CLOSED;
        }

        return $this->ticket->getStatus();
    }
}
