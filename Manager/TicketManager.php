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

use DateMalformedIntervalStringException;
use DateTime;
use DateInterval;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TicketManager implements TicketManagerInterface
{
    use PermissionManagerTrait;
    use UserManagerTrait;

    private ?TranslatorInterface $translator = null;

    private string $translationDomain = 'HackzillaTicketBundle';

    private ?ObjectManager $objectManager = null;

    private ObjectRepository $ticketRepository;

    private ObjectRepository $messageRepository;

    /**
     * TicketManager constructor.
     */
    public function __construct(private readonly string $ticketClass, private readonly string $ticketMessageClass)
    {
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

        if ($this->ticketClass !== '' && $this->ticketClass !== '0') {
            $this->ticketRepository = $objectManager->getRepository($this->ticketClass);
        }

        if ($this->ticketMessageClass !== '' && $this->ticketMessageClass !== '0') {
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
    public function createTicket(): TicketInterface
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
     * @param TicketInterface|null $ticket
     *
     * @return TicketMessageInterface
     */
    public function createMessage(?TicketInterface $ticket = null): TicketMessageInterface
    {
        /* @var TicketMessageInterface $ticket */
        $message = new $this->ticketMessageClass();

        if ($ticket instanceof TicketInterface) {
            $message->setPriority($ticket->getPriority());
            $message->setStatus($ticket->getStatus());
            $message->setTicket($ticket);
        } else {
            $message->setStatus(TicketMessageInterface::STATUS_OPEN);
        }

        return $message;
    }

    /**
     * @param TicketInterface $ticket
     * @param TicketMessageInterface|null $message
     *
     * @return void
     */
    public function updateTicket(TicketInterface $ticket, ?TicketMessageInterface $message = null): void
    {
        if (null === $ticket->getId()) {
            $this->objectManager->persist($ticket);
        }
        if ($message instanceof TicketMessageInterface) {
            $message->setTicket($ticket);
            $this->objectManager->persist($message);
            $ticket->setPriority($message->getPriority());
        }
        $this->objectManager->flush();
    }

    /**
     * Delete a ticket from the database.
     */
    public function deleteTicket(TicketInterface $ticket): void
    {
        $this->objectManager->remove($ticket);
        $this->objectManager->flush();
    }

    /**
     * Find all tickets in the database.
     *
     * @return TicketInterface[]
     */
    public function findTickets(): array
    {
        return $this->ticketRepository->findAll();
    }

    /**
     * Find ticket in the database.
     *
     * @param int $ticketId
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
     * @return TicketMessageInterface|null
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
    public function findTicketsBy(array $criteria): array
    {
        return $this->ticketRepository->findBy($criteria);
    }

    /**
     * @param $ticketStatus
     * @param $ticketPriority
     *
     * @return QueryBuilder
     */
    public function getTicketListQuery($ticketStatus, $ticketPriority = null): QueryBuilder
    {
        $query = $this->ticketRepository->createQueryBuilder('t')
            ->innerjoin('t.userCreated', 'u')
            ->orderBy('t.lastMessage', 'DESC')
        ;

        match ($ticketStatus) {
            TicketMessageInterface::STATUS_CLOSED => $query
                ->andWhere('t.status = :status')
                ->setParameter('status', TicketMessageInterface::STATUS_CLOSED),
            default => $query
                ->andWhere('t.status != :status')
                ->setParameter('status', TicketMessageInterface::STATUS_CLOSED),
        };

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
     * @throws DateMalformedIntervalStringException
     */
    public function getResolvedTicketOlderThan(int $days)
    {
        $closeBeforeDate = new DateTime();
        $closeBeforeDate->sub(new DateInterval('P'.$days.'D'));

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
     * @return int|string|false
     */
    public function getTicketStatus(string $statusStr): int|string|false
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
     * @return int|string|false
     */
    public function getTicketPriority(string $priorityStr): int|string|false
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
