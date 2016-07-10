<?php

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\TicketRole;

class TicketManager implements TicketManagerInterface
{
    private $objectManager;
    private $repository;
    private $ticketClass;
    private $ticketMessageClass;

    /**
     * TicketManager constructor.
     *
     * @param ObjectManager $om
     * @param string        $ticketClass
     * @param string        $ticketMessageClass
     */
    public function __construct(ObjectManager $om, $ticketClass, $ticketMessageClass)
    {
        $this->objectManager = $om;
        $this->repository = $om->getRepository($ticketClass);
        $this->ticketClass = $ticketClass;
        $this->ticketMessageClass = $ticketMessageClass;
    }

    /**
     * Create a new instance of Ticket entity.
     *
     * @return TicketInterface
     */
    public function createTicket()
    {
        return new $this->ticketClass();
    }

    /**
     * Create a new instance of TicketMessage Entity.
     *
     * @param TicketInterface $ticket
     *
     * @return TicketMessageInterface
     */
    public function createMessage(TicketInterface $ticket = null)
    {
        $message = new $this->ticketMessageClass();
        $message->setStatus(TicketMessage::STATUS_OPEN);

        if ($ticket) {
            $message->setTicket($ticket);
            $message->setPriority($ticket->getPriority());
        }

        return $message;
    }

    /**
     * Update or Create a Ticket in the database
     * Update or Create a TicketMessage in the database.
     *
     * @param TicketInterface        $ticket
     * @param TicketMessageInterface $message
     *
     * @return TicketInterface
     */
    public function updateTicket(TicketInterface $ticket, TicketMessageInterface $message = null)
    {
        if (is_null($ticket->getId())) {
            $this->objectManager->persist($ticket);
        }
        if (!\is_null($message)) {
            $message->setTicket($ticket);
            $this->objectManager->persist($message);
        }
        $this->objectManager->flush();

        return $ticket;
    }

    /**
     * Delete a ticket from the database.
     *
     * @param TicketInterface $ticket*
     */
    public function deleteTicket(TicketInterface $ticket)
    {
        $this->objectManager->remove($ticket);
        $this->objectManager->flush();
    }

    /**
     * Find all tickets in the database.
     *
     * @return array|TicketInterface[]
     */
    public function findTickets()
    {
        return $this->repository->findAll();
    }

    /**
     * Find ticket in the database.
     *
     * @param int $ticketId
     *
     * @return TicketInterface
     */
    public function getTicket($ticketId)
    {
        return $this->repository->find($ticketId);
    }

    /**
     * Find ticket by criteria.
     *
     * @param array $criteria
     *
     * @return array|TicketInterface[]
     */
    public function findTicketsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * @param UserManagerInterface $userManager
     * @param int                  $ticketStatus
     * @param int                  $ticketPriority
     *
     * @return mixed
     */
    public function getTicketList(UserManagerInterface $userManager, $ticketStatus, $ticketPriority = null)
    {
        $query = $this->repository->createQueryBuilder('t')
//            ->select($this->ticketClass.' t')
            ->orderBy('t.lastMessage', 'DESC');

        switch ($ticketStatus) {
            case TicketMessage::STATUS_CLOSED:
                $query
                    ->andWhere('t.status = :status')
                    ->setParameter('status', TicketMessageInterface::STATUS_CLOSED);
                break;

            case TicketMessage::STATUS_OPEN:
            default:
                $query
                    ->andWhere('t.status != :status')
                    ->setParameter('status', TicketMessageInterface::STATUS_CLOSED);
        }

        if ($ticketPriority) {
            $query
                ->andWhere('t.priority = :priority')
                ->setParameter('priority', $ticketPriority);
        }

        $user = $userManager->getCurrentUser();

        if (\is_object($user)) {
            if (!$userManager->hasRole($user, TicketRole::ADMIN)) {
                $query
                    ->andWhere('t.userCreated = :userId')
                    ->setParameter('userId', $user->getId());
            }
        } else {
            // anonymous user
            $query
                ->andWhere('t.userCreated = :userId')
                ->setParameter('userId', 0);
        }

        return $query;
    }

    /**
     * @param int $days
     *
     * @return mixed
     */
    public function getResolvedTicketOlderThan($days)
    {
        $closeBeforeDate = new \DateTime();
        $closeBeforeDate->sub(new \DateInterval('P'.$days.'D'));

        $query = $this->repository->createQueryBuilder('t')
//            ->select($this->ticketClass.' t')
            ->where('t.status = :status')
            ->andWhere('t.lastMessage < :closeBeforeDate')
            ->setParameter('status', TicketMessageInterface::STATUS_RESOLVED)
            ->setParameter('closeBeforeDate', $closeBeforeDate);

        return $query->getQuery()->getResult();
    }

    /**
     * Lookup status code.
     *
     * @param object $translator
     * @param string $statusStr
     *
     * @return int
     */
    public function getTicketStatus($translator, $statusStr)
    {
        static $statuses = false;

        if ($statuses === false) {
            $statuses = [];

            foreach (TicketMessageInterface::STATUSES as $id => $value) {
                $statuses[$id] = $translator->trans($value);
            }
        }

        return \array_search($statusStr, $statuses);
    }

    /**
     * Lookup priority code.
     *
     * @param object $translator
     * @param string $priorityStr
     *
     * @return int
     */
    public function getTicketPriority($translator, $priorityStr)
    {
        static $priorities = false;

        if ($priorities === false) {
            $priorities = [];

            foreach (TicketMessageInterface::PRIORITIES as $id => $value) {
                $priorities[$id] = $translator->trans($value);
            }
        }

        return \array_search($priorityStr, $priorities);
    }
}
