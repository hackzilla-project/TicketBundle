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

use Doctrine\Common\Persistence\ObjectManager;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @final since hackzilla/ticket-bundle 3.x.
 */
class TicketManager implements TicketManagerInterface
{
    private $translator;

    /**
     * NEXT_MAJOR: Remove this property and replace its usages with "HackzillaTicketBundle".
     *
     * @var string
     */
    private $translationDomain = 'messages';

    private $objectManager;

    private $ticketRepository;

    private $messageRepository;

    private $ticketClass;

    private $ticketMessageClass;

    /**
     * TicketManager constructor.
     *
     * @param string $ticketClass
     * @param string $ticketMessageClass
     */
    public function __construct($ticketClass, $ticketMessageClass)
    {
        $this->ticketClass = $ticketClass;
        $this->ticketMessageClass = $ticketMessageClass;
    }

    /**
     * @return $this
     */
    public function setEntityManager(ObjectManager $om)
    {
        $this->objectManager = $om;
        $this->ticketRepository = $om->getRepository($this->ticketClass);
        $this->messageRepository = $om->getRepository($this->ticketMessageClass);

        return $this;
    }

    /**
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @param string $translationDomain
     *
     * @return $this
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;

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
    public function createMessage(TicketInterface $ticket = null)
    {
        /* @var TicketMessageInterface $ticket */
        $message = new $this->ticketMessageClass();

        if ($ticket) {
            $message->setPriority($ticket->getPriority());
            $message->setStatus($ticket->getStatus());
            $message->setTicket($ticket);
        } else {
            $message->setStatus(TicketMessage::STATUS_OPEN);
        }

        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTicket(TicketInterface $ticket, TicketMessageInterface $message = null)
    {
        if (null === $ticket->getId()) {
            $this->objectManager->persist($ticket);
        }
        if (null !== $message) {
            $message->setTicket($ticket);
            $this->objectManager->persist($message);
        }
        $this->objectManager->flush();

        // NEXT_MAJOR: Remove the `return` statement.
        return $ticket;
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
     * @return array|TicketInterface[]
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
     * @return TicketInterface
     */
    public function getTicketById($ticketId)
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
    public function getMessageById($ticketMessageId)
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
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since hackzilla/ticket-bundle 3.3, use `getTicketListQuery()` instead.
     *
     * @param int $ticketStatus
     * @param int $ticketPriority
     *
     * @return mixed
     */
    public function getTicketList(UserManagerInterface $userManager, $ticketStatus, $ticketPriority = null)
    {
        @trigger_error(sprintf(
            'Method `%s()` is deprecated since hackzilla/ticket-bundle 3.3 and will be removed in version 4.0.'
            .' Use `%s::getTicketListQuery()` instead.',
            __METHOD__,
            __CLASS__
        ), E_USER_DEPRECATED);

        return $this->getTicketListQuery($userManager, $ticketStatus, $ticketPriority);
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketListQuery(UserManagerInterface $userManager, $ticketStatus, $ticketPriority = null)
    {
        $query = $this->ticketRepository->createQueryBuilder('t')
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

        $query = $this->ticketRepository->createQueryBuilder('t')
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

        return array_search($statusStr, $statuses);
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

        return array_search($priorityStr, $priorities);
    }
}
