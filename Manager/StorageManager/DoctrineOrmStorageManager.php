<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\StorageManager;

use Doctrine\Common\Persistence\ObjectManager;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Hackzilla\TicketMessage\Manager\StorageManagerInterface;
use Hackzilla\TicketMessage\Manager\UserManagerInterface;
use Hackzilla\TicketMessage\Model\TicketMessageInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class DoctrineOrmStorageManager implements StorageManagerInterface
{
    private $objectManager;
    private $ticketRepository;
    private $messageRepository;

    /**
     * @param ObjectManager        $om
     * @param UserManagerInterface $userManager
     * @param string               $ticketClass
     * @param string               $ticketMessageClass
     *
     * @return $this
     */
    public function __construct(ObjectManager $om, UserManagerInterface $userManager, $ticketClass, $ticketMessageClass)
    {
        $this->objectManager = $om;
        $this->userManager = $userManager;
        $this->ticketRepository = $om->getRepository($ticketClass);
        $this->messageRepository = $om->getRepository($ticketMessageClass);

        return $this;
    }

    public function persist($entity)
    {
        $this->objectManager->persist($entity);
    }

    public function remove($entity)
    {
        $this->objectManager->remove($entity);
    }

    public function flush()
    {
        $this->objectManager->flush();
    }

    public function getTicketById($ticketId)
    {
        return $this->ticketRepository->find($ticketId);
    }

    public function getMessageById($ticketMessageId)
    {
        return $this->messageRepository->find($ticketMessageId);
    }

    /**
     * {@inheritdoc}
     */
    public function findTicketsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->ticketRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketList($ticketStatus, $ticketPriority = null)
    {
        $query = $this->ticketRepository->createQueryBuilder('t')
//            ->select($this->ticketClass.' t')
            ->orderBy('t.lastMessage', 'DESC');

        switch ($ticketStatus) {
            case TicketMessageInterface::STATUS_CLOSED:
                $query
                    ->andWhere('t.status = :status')
                    ->setParameter('status', TicketMessageInterface::STATUS_CLOSED);
                break;

            case TicketMessageInterface::STATUS_OPEN:
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

        $user = $this->userManager ? $this->userManager->getCurrentUser() : null;

        if (\is_object($user)) {
            if (!$this->userManager->hasRole($user)) {
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

        return new Pagerfanta(new DoctrineORMAdapter($query));
    }

    /**
     * {@inheritdoc}
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
     * @inheritDoc
     */
    public function getSortableFields()
    {
        return [
            'id',
            'subject',
            'status',
            'priority',
            'userCreated',
            'createdAt',
            'lastMessage',
        ];
    }
}
