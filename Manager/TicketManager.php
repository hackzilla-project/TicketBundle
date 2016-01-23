<?php

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Hackzilla\Bundle\TicketBundle\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class TicketManager implements TicketManagerInterface
{
    private $objectManager;
    private $repository;

    public function __construct(ObjectManager $om)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository('HackzillaTicketBundle:Ticket');
    }

    /**
     * Create a new instance of Ticket entity.
     */
    public function createTicket()
    {
        return new Ticket();
    }

    /**
     * Create a new instance of TicketMessage Entity.
     */
    public function createMessage()
    {
        return new TicketMessage();
    }

    /**
     * Update or Create a Ticket in the database
     * Update or Create a TicketMessage in the database.
     *
     * @param Ticket        $ticket
     * @param TicketMessage $message
     *
     * @return Ticket
     */
    public function updateTicket(Ticket $ticket, TicketMessage $message = null)
    {
        if (!\is_null($ticket)) {
            $this->objectManager->persist($ticket);
        }
        if (!\is_null($message)) {
            $this->objectManager->persist($message);
        }
        $this->objectManager->flush();

        return $ticket;
    }

    /**
     * Delete a ticket from the database.
     *
     * @param Ticket $ticket
     */
    public function deleteTicket(Ticket $ticket)
    {
        $this->objectManager->remove($ticket);
        $this->objectManager->flush();
    }

    /**
     * Find all tickets in the database.
     *
     * @return array|\Hackzilla\Bundle\TicketBundle\Entity\Ticket[]
     */
    public function findTickets()
    {
        return $this->repository->findAll();
    }

    /**
     * Find ticket by criteria.
     *
     * @param array $criteria
     *
     * @return array|\Hackzilla\Bundle\TicketBundle\Entity\Ticket[]
     */
    public function findTicketsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }
}
