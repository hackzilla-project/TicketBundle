<?php

namespace Hackzilla\Bundle\TicketBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="Hackzilla\Bundle\TicketBundle\Entity\TicketRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Ticket
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_created_id", type="integer")
     */
    protected $userCreated;
    protected $userCreatedObject;

    /**
     * @var integer
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
     * @Assert\NotBlank()
     */
    protected $subject;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint")
     */
    protected $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="smallint")
     */
    protected $priority;

    /**
     * @ORM\OneToMany(targetEntity="TicketMessage",  mappedBy="ticket")
     */
    protected $messages;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->messages = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Set status string
     *
     * @param string $status
     */
    public function setStatusString($status)
    {
        $status = \array_search(\strtolower($status), TicketMessage::$statuses);

        if ($status > 0) {
            $this->setStatus($status);
        }
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get status string
     *
     * @return string
     */
    public function getStatusString()
    {
        if (isset(TicketMessage::$statuses[$this->status])) {
            return TicketMessage::$statuses[$this->status];
        }

        return TicketMessage::$statuses[0];
    }

    /**
     * Set priority
     *
     * @param integer $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * Set priority string
     *
     * @param string $priority
     */
    public function setPriorityString($priority)
    {
        $priority = \array_search(\strtolower($priority), TicketMessage::$priorities);

        if ($priority > 0) {
            $this->setPriority($priority);
        }
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Get priority string
     *
     * @return string
     */
    public function getPriorityString()
    {
        if (isset(TicketMessage::$priorities[$this->priority])) {
            return TicketMessage::$priorities[$this->priority];
        }

        return TicketMessage::$priorities[0];
    }

    /**
     * Set userCreated
     *
     * @param integer|object $userCreated
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
     * Get userCreated
     *
     * @return integer
     */
    public function getUserCreated()
    {
        return $this->userCreated;
    }

    /**
     * Get userCreated object
     *
     * @return object
     */
    public function getUserCreatedObject()
    {
        return $this->userCreatedObject;
    }

    /**
     * Set lastUser
     *
     * @param integer|object $lastUser
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
     * Get lastUser
     *
     * @return integer
     */
    public function getLastUser()
    {
        return $this->lastUser;
    }

    /**
     * Get lastUser object
     *
     * @return object
     */
    public function getLastUserObject()
    {
        return $this->lastUserObject;
    }

    /**
     * Set lastMessage
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
     * Get lastMessage
     *
     * @return \DateTime
     */
    public function getLastMessage()
    {
        return $this->lastMessage;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Ticket
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set subject
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
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Add messages
     *
     * @param TicketMessage $messages
     *
     * @return Ticket
     */
    public function addMessage(TicketMessage $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param TicketMessage $messages
     */
    public function removeMessage(TicketMessage $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
