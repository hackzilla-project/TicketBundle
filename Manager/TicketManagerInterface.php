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
use Doctrine\Persistence\ObjectManager;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Symfony\Component\Translation\Translator;

interface TicketManagerInterface
{
    public function setObjectManager(ObjectManager $objectManager): void;

    public function setTranslator(Translator $translator);

    public function createTicket();

    public function createMessage(?TicketInterface $ticket = null);

    public function updateTicket(TicketInterface $ticket, ?TicketMessageInterface $message = null): void;

    public function deleteTicket(TicketInterface $ticket);

    public function getTicketById($ticketId);

    public function getMessageById($ticketMessageId);

    public function findTickets();

    public function findTicketsBy(array $criteria);

    public function getTicketListQuery(UserManagerInterface $userManager, $ticketStatus, $ticketPriority = null): QueryBuilder;

    /**
     * @param int $days
     *
     * @return mixed
     */
    public function getResolvedTicketOlderThan($days);

    /**
     * Lookup status code.
     *
     * @param string $statusStr
     *
     * @return int
     */
    public function getTicketStatus($statusStr);

    /**
     * Lookup priority code.
     *
     * @param string $priorityStr
     *
     * @return int
     */
    public function getTicketPriority($priorityStr);
}
