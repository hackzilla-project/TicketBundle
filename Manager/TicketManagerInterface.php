<?php

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Hackzilla\Bundle\TicketBundle\Entity\Ticket;

interface TicketManagerInterface
{
	public function createTicket();

	public function createMessage();

	public function updateTicket(Ticket $ticket);

	public function deleteTicket(Ticket $ticket);

	public function findTickets();

	public function findTicketsBy(array $criteria);
}
