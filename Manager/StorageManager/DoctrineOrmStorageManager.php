<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\StorageManager;

use Doctrine\Common\Persistence\ObjectManager;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Hackzilla\TicketMessage\Manager\StorageManagerInterface;
use Hackzilla\TicketMessage\Manager\UserManagerInterface;
use Hackzilla\TicketMessage\Model\TicketMessageInterface;
use Hackzilla\TicketMessage\Model\UserInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class DoctrineOrmStorageManager implements StorageManagerInterface
{
    /** @var string */
    private $ticketClass;

    /** @var string */
    private $ticketMessageClass;

    /** @var string */
    private $userClass;

    private $objectManager;
    private $ticketRepository;
    private $messageRepository;
    private $userRepository;

    /**
     * @param ObjectManager        $om
     * @param UserManagerInterface $userManager
     * @param string               $ticketClass
     * @param string               $ticketMessageClass
     * @param string               $userClass
     *
     * @return $this
     */
    public function __construct(
        $ticketClass,
        $ticketMessageClass,
        $userClass
    ) {
        $this->ticketClass = $ticketClass;
        $this->ticketMessageClass = $ticketMessageClass;
        $this->userClass = $userClass;

        return $this;
    }

    /**
     * @param ObjectManager $om
     *
     * @return $this
     */
    public function setEntityManager(ObjectManager $om)
    {
        $this->objectManager = $om;

        $this->ticketRepository = $om->getRepository($this->ticketClass);
        $this->messageRepository = $om->getRepository($this->ticketMessageClass);
        $this->userRepository = $om->getRepository($this->userClass);

        return $this;
    }

    /**
     * @param UserManagerInterface $userManager
     *
     * @return $this
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;

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
            ->where('t.status = :status')
            ->andWhere('t.lastMessage < :closeBeforeDate')
            ->setParameter('status', TicketMessageInterface::STATUS_RESOLVED)
            ->setParameter('closeBeforeDate', $closeBeforeDate);

        return $query->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getUser($username)
    {
        return $this->userRepository->findOneBy([
            'username' => $username,
        ]);
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
