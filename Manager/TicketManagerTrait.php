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

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface;

trait TicketManagerTrait
{
    private ?TicketManagerInterface $ticketManager;

    protected function getTicketManager(): TicketManagerInterface
    {
        return $this->ticketManager;
    }
}
