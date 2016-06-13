<?php

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;

interface TicketManagerInterface
{
    public function createTicket();

    public function createMessage();

    public function updateTicket(TicketInterface $ticket);

    public function deleteTicket(TicketInterface $ticket);

    public function findTickets();

    public function findTicketsBy(array $criteria);

    /**
     * @param UserManagerInterface $userManager
     * @param int                  $ticketStatus
     * @param int                  $ticketPriority
     *
     * @return mixed
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
     * @param object $translator
     * @param string $statusStr
     *
     * @return int
     */
    public function getTicketStatus($translator, $statusStr);

    /**
     * Lookup priority code.
     *
     * @param object $translator
     * @param string $priorityStr
     *
     * @return int
     */
    public function getTicketPriority($translator, $priorityStr);
}
