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

namespace Hackzilla\Bundle\TicketBundle\Event;

use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class TicketEvent extends Event
{
    public function __construct(protected TicketInterface $ticket)
    {
    }

    public function getTicket(): TicketInterface
    {
        return $this->ticket;
    }
}
