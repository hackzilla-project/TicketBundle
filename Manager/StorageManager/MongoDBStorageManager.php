<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\StorageManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Hackzilla\TicketMessage\Manager\StorageManagerInterface;
use Pagerfanta\Adapter\MongoAdapter;
use Pagerfanta\Pagerfanta;

class MongoDBStorageManager implements StorageManagerInterface
{
    /** @var string */
    private $ticketClass;

    private $documentManager;
    private $ticketRepository;
    private $messageRepository;

    /**
     * @param DocumentManager      $om
     * @param UserManagerInterface $userManager
     * @param string               $ticketClass
     * @param string               $ticketMessageClass
     *
     * @return $this
     */
    public function __construct(DocumentManager $dm, UserManagerInterface $userManager, $ticketClass, $ticketMessageClass)
    {
        $this->ticketClass = $ticketClass;

        $this->documentManager = $dm;
        $this->userManager = $userManager;
        $this->ticketRepository = $dm->getRepository($ticketClass);
        $this->messageRepository = $dm->getRepository($ticketMessageClass);

        return $this;
    }

    public function persist($entity)
    {
        $this->documentManager->persist($entity);
    }

    public function remove($entity)
    {
        $this->documentManager->remove($entity);
    }

    public function flush()
    {
        $this->documentManager->flush();
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
    public function getTicketList($ticketStatus, $ticketPriority = null, array $orderBy = null)
    {
        $query = $this->ticketRepository->createQueryBuilder('t')
            ->select('t')
        ;

        if (is_array($orderBy)) {
            foreach ($orderBy as $field => $direction) {
                if (!$direction) {
                    continue;
                }

                $query->addOrderBy('t.' . $field, strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC');
            }
        } else {
            $query->addOrderBy('t.lastMessage', 'DESC');
        }

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

        return new Pagerfanta(new MongoAdapter($query));
    }

    /**
     * {@inheritdoc}
     */
    public function getResolvedTicketOlderThan($days)
    {
        $closeBeforeDate = new \DateTime();
        $closeBeforeDate->sub(new \DateInterval('P'.$days.'D'));

        $query = $this->ticketRepository->createQueryBuilder('t')
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
