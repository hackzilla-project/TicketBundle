<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hackzilla\TicketMessage\Model\TicketInterface;
use Hackzilla\TicketMessage\Model\TicketMessageInterface;

/**
 * Ticket.
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity)
 */
class Ticket implements TicketInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="user_created_id", type="integer")
     */
    protected $userCreated;
    protected $userCreatedObject;

    /**
     * @var int
     *
     * @ORM\Column(name="last_user_id", type="integer")
     */
    protected $lastUser;
    protected $lastUserObject;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_message", type="datetime")
     */
    protected $lastMessage;

    /**
     * @ORM\Column(name="subject", type="string", length=255)
     */
    protected $subject;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="smallint")
     */
    protected $status;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="smallint")
     */
    protected $priority;

    /**
     * @ORM\OneToMany(targetEntity="TicketMessage",  mappedBy="ticket")
     */
    protected $messages;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->messages = [];
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set status string.
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatusString($status)
    {
        $status = \array_search(\strtolower($status), TicketMessageInterface::$statuses);

        if ($status > 0) {
            $this->setStatus($status);
        }

        return $this;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get status string.
     *
     * @return string
     */
    public function getStatusString()
    {
        if (isset(TicketMessageInterface::STATUSES[$this->status])) {
            return TicketMessageInterface::STATUSES[$this->status];
        }

        return TicketMessageInterface::STATUSES[0];
    }

    /**
     * Set priority.
     *
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Set priority string.
     *
     * @param string $priority
     */
    public function setPriorityString($priority)
    {
        $priority = \array_search(\strtolower($priority), TicketMessageInterface::PRIORITIES);

        if ($priority > 0) {
            $this->setPriority($priority);
        }

        return $this;
    }

    /**
     * Get priority.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get priority string.
     *
     * @return string
     */
    public function getPriorityString()
    {
        if (isset(TicketMessageInterface::PRIORITIES[$this->priority])) {
            return TicketMessageInterface::PRIORITIES[$this->priority];
        }

        return TicketMessageInterface::PRIORITIES[0];
    }

    /**
     * Set userCreated.
     *
     * @param int|object $userCreated
     *
     * @return Ticket
     */
    public function setUserCreated($userCreated)
    {
        if (\is_object($userCreated)) {
            $this->userCreatedObject = $userCreated;
            $this->userCreated = $userCreated->getId();
        } else {
            $this->userCreatedObject = null;
            $this->userCreated = $userCreated;
        }

        return $this;
    }

    /**
     * Get userCreated.
     *
     * @return int
     */
    public function getUserCreated()
    {
        return $this->userCreated;
    }

    /**
     * Get userCreated object.
     *
     * @return object
     */
    public function getUserCreatedObject()
    {
        return $this->userCreatedObject;
    }

    /**
     * Set lastUser.
     *
     * @param int|object $lastUser
     *
     * @return Ticket
     */
    public function setLastUser($lastUser)
    {
        if (\is_object($lastUser)) {
            $this->lastUserObject = $lastUser;
            $this->lastUser = $lastUser->getId();
        } else {
            $this->lastUserObject = null;
            $this->lastUser = $lastUser;
        }

        return $this;
    }

    /**
     * Get lastUser.
     *
     * @return int
     */
    public function getLastUser()
    {
        return $this->lastUser;
    }

    /**
     * Get lastUser object.
     *
     * @return object
     */
    public function getLastUserObject()
    {
        return $this->lastUserObject;
    }

    /**
     * Set lastMessage.
     *
     * @param \DateTime $lastMessage
     *
     * @return Ticket
     */
    public function setLastMessage($lastMessage)
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * Get lastMessage.
     *
     * @return \DateTime
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Ticket
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set subject.
     *
     * @param string $subject
     *
     * @return Ticket
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Add messages.
     *
     * @param TicketMessageInterface $messages
     *
     * @return Ticket
     */
    public function addMessage(TicketMessageInterface $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Get messages.
     *
     * @return TicketMessageInterface[]
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
