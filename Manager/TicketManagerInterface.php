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

use Doctrine\ORM\QueryBuilder;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;

interface TicketManagerInterface
{
    public function createTicket();

    public function createMessage(?TicketInterface $ticket = null);

    public function updateTicket(TicketInterface $ticket, ?TicketMessageInterface $message = null): void;

    public function deleteTicket(TicketInterface $ticket);

    public function getTicketById($ticketId): ?TicketInterface;

    public function getMessageById($ticketMessageId): ?TicketMessageInterface;

    public function findTickets();

    public function findTicketsBy(array $criteria);

    public function getTicketListQuery($ticketStatus, $ticketPriority = null): QueryBuilder;

    /**
     * @param int $days
     *
     * @return mixed
     */
    public function getResolvedTicketOlderThan(int $days): mixed;

    /**
     * Lookup status code.
     *
     * @param string $statusStr
     *
     * @return int|string|bool
     */
    public function getTicketStatus(string $statusStr): int|string|bool;

    /**
     * Lookup priority code.
     *
     * @param string $priorityStr
     *
     * @return int|string|bool
     */
    public function getTicketPriority(string $priorityStr): int|string|bool;
}
