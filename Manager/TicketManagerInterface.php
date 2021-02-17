<?php

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager as LegacyObjectManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectManager;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @method QueryBuilder getTicketListQuery(UserManagerInterface $userManager, $ticketStatus, $ticketPriority = null)
 * @method void         setObjectManager(ObjectManager $objectManager)
 */
interface TicketManagerInterface
{
    /**
     * @deprecated since hackzilla/ticket-bundle 3.x, use `setObjectManager()` instead.
     */
    public function setEntityManager(LegacyObjectManager $om);

    public function setTranslator(TranslatorInterface $translator);

    public function createTicket();

    public function createMessage(TicketInterface $ticket = null);

    /**
     * NEXT_MAJOR: Declare `void` as return type.
     */
    public function updateTicket(TicketInterface $ticket, TicketMessageInterface $message = null);

    public function deleteTicket(TicketInterface $ticket);

    public function getTicketById($ticketId);

    public function getMessageById($ticketMessageId);

    public function findTickets();

    public function findTicketsBy(array $criteria);

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since hackzilla/ticket-bundle 3.3, use `getTicketListQuery()` instead.
     *
     * @param int $ticketStatus
     * @param int $ticketPriority
     *
     * @return QueryBuilder
     */
    public function getTicketList(UserManagerInterface $userManager, $ticketStatus, $ticketPriority = null);

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
