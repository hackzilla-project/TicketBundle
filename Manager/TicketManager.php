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
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TicketManager implements TicketManagerInterface
{
    use PermissionManagerTrait;
    use UserManagerTrait;

    private $translator;

    private $translationDomain = 'HackzillaTicketBundle';

    private $objectManager;

    private $ticketRepository;

    private $messageRepository;

    private $ticketClass;

    private $ticketMessageClass;

    /**
     * TicketManager constructor.
     */
    public function __construct(string $ticketClass, string $ticketMessageClass)
    {
        $this->ticketClass = $ticketClass;
        $this->ticketMessageClass = $ticketMessageClass;
    }

    public function setLogger(LoggerInterface $logger): self
    {
        if (!class_exists($this->ticketClass)) {
            $logger->error(sprintf('Ticket entity %s doesn\'t exist', $this->ticketClass));
        }
        if (!class_exists($this->ticketMessageClass)) {
            $logger->error(sprintf('Message entity %s doesn\'t exist', $this->ticketMessageClass));
        }

        return $this;
    }

    public function setObjectManager(ObjectManager $objectManager): self
    {
        $this->objectManager = $objectManager;

        if ($this->ticketClass) {
            $this->ticketRepository = $objectManager->getRepository($this->ticketClass);
        }

        if ($this->ticketMessageClass) {
            $this->messageRepository = $objectManager->getRepository($this->ticketMessageClass);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Create a new instance of Ticket entity.
     *
     * @return TicketInterface
     */
    public function createTicket()
    {
        /* @var TicketInterface $ticket */
        $ticket = new $this->ticketClass();
        $ticket->setPriority(TicketMessageInterface::PRIORITY_MEDIUM);
        $ticket->setStatus(TicketMessageInterface::STATUS_OPEN);

        return $ticket;
    }

    /**
     * Create a new instance of TicketMessage Entity.
     *
     * @param TicketInterface $ticket
     *
     * @return TicketMessageInterface
     */
    public function createMessage(?TicketInterface $ticket = null)
    {
        /* @var TicketMessageInterface $ticket */
        $message = new $this->ticketMessageClass();

        if ($ticket) {
            $message->setPriority($ticket->getPriority());
            $message->setStatus($ticket->getStatus());
            $message->setTicket($ticket);
        } else {
            $message->setStatus(TicketMessageInterface::STATUS_OPEN);
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTicket(TicketInterface $ticket, ?TicketMessageInterface $message = null): void
    {
        if (null === $ticket->getId()) {
            $this->objectManager->persist($ticket);
        }
        if (null !== $message) {
            $message->setTicket($ticket);
            $this->objectManager->persist($message);
            $ticket->setPriority($message->getPriority());
        }
        $this->objectManager->flush();
    }

    /**
     * Delete a ticket from the database.
     */
    public function deleteTicket(TicketInterface $ticket)
    {
        $this->objectManager->remove($ticket);
        $this->objectManager->flush();
    }

    /**
     * Find all tickets in the database.
     *
     * @return TicketInterface[]
     */
    public function findTickets()
    {
        return $this->ticketRepository->findAll();
    }

    /**
     * Find ticket in the database.
     *
     * @param int $ticketId
     *
     * @return ?TicketInterface
     */
    public function getTicketById($ticketId): ?TicketInterface
    {
        return $this->ticketRepository->find($ticketId);
    }

    /**
     * Find message in the database.
     *
     * @param int $ticketMessageId
     *
     * @return TicketMessageInterface
     */
    public function getMessageById($ticketMessageId): ?TicketMessageInterface
    {
        return $this->messageRepository->find($ticketMessageId);
    }

    /**
     * Find ticket by criteria.
     *
     * @return array|TicketInterface[]
     */
    public function findTicketsBy(array $criteria)
    {
        return $this->ticketRepository->findBy($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketListQuery($ticketStatus, $ticketPriority = null): QueryBuilder
    {
        $query = $this->ticketRepository->createQueryBuilder('t')
            ->innerjoin('t.userCreated', 'u')
            ->orderBy('t.lastMessage', 'DESC')
        ;

        switch ($ticketStatus) {
            case TicketMessageInterface::STATUS_CLOSED:
                $query
                    ->andWhere('t.status = :status')
                    ->setParameter('status', TicketMessageInterface::STATUS_CLOSED)
                ;

                break;

            case TicketMessageInterface::STATUS_OPEN:
            default:
                $query
                    ->andWhere('t.status != :status')
                    ->setParameter('status', TicketMessageInterface::STATUS_CLOSED)
                ;
        }

        if ($ticketPriority) {
            $query
                ->andWhere('t.priority = :priority')
                ->setParameter('priority', $ticketPriority)
            ;
        }

        // add permissions check and return updated query
        return $this->getPermissionManager()->addUserPermissionsCondition(
            $query,
            $this->getUserManager()->getCurrentUser(),
        );
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

        $query = $this->ticketRepository->createQueryBuilder('t')
//            ->select($this->ticketClass.' t')
            ->where('t.status = :status')
            ->andWhere('t.lastMessage < :closeBeforeDate')
            ->setParameter('status', TicketMessageInterface::STATUS_RESOLVED)
            ->setParameter('closeBeforeDate', $closeBeforeDate)
        ;

        return $query->getQuery()->getResult();
    }

    /**
     * Lookup status code.
     *
     * @param string $statusStr
     *
     * @return int
     */
    public function getTicketStatus($statusStr)
    {
        static $statuses = false;

        if (false === $statuses) {
            $statuses = [];

            foreach (TicketMessageInterface::STATUSES as $id => $value) {
                $statuses[$id] = $this->translator->trans($value, [], $this->translationDomain);
            }
        }

        return array_search($statusStr, $statuses, true);
    }

    /**
     * Lookup priority code.
     *
     * @param string $priorityStr
     *
     * @return int
     */
    public function getTicketPriority($priorityStr)
    {
        static $priorities = false;

        if (false === $priorities) {
            $priorities = [];

            foreach (TicketMessageInterface::PRIORITIES as $id => $value) {
                $priorities[$id] = $this->translator->trans($value, [], $this->translationDomain);
            }
        }

        return array_search($priorityStr, $priorities, true);
    }
}
