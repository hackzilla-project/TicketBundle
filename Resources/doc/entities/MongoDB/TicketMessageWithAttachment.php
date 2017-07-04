<?php

namespace AppBundle\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Hackzilla\TicketMessage\Model\TicketFeature\MessageAttachmentInterface;
use Hackzilla\TicketMessage\Model\TicketInterface;
use Hackzilla\TicketMessage\Model\TicketMessageInterface;
use Hackzilla\TicketMessage\Model\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Message.
 *
 * @MongoDB\Document
 */
class TicketMessage implements TicketMessageInterface, MessageAttachmentInterface
{
    /**
     * @var int
     *
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Ticket")
     */
    protected $ticket;

    /**
     * @var int
     *
     * @MongoDB\Field(name="user_id", type="int")
     */
    protected $user;
    protected $userObject;

    /**
     * @var string
     *
     * @MongoDB\Field(name="message", type="string", nullable=true)
     */
    protected $message;

    /**
     * @var int
     *
     * @MongoDB\Field(name="status", type="int")
     */
    protected $status;

    /**
     * @var int
     *
     * @MongoDB\Field(name="priority", type="int")
     */
    protected $priority;

    /**
     * NOTE: This field is not persisted to database!
     *
     * @var File
     *
     * @Vich\UploadableField(mapping="ticket_message_attachment", fileNameProperty="attachmentName", size="attachmentSize")
     */
    protected $attachmentFile;

    /**
     * @var string
     *
     * @MongoDB\Field(name="attachment_name", type="string", length=255, nullable=true)
     */
    protected $attachmentName;

    /**
     * @var int
     *
     * @MongoDB\Field(name="attachment_size", type="int", nullable=true)
     */
    protected $attachmentSize;

    /**
     * @var string
     *
     * @MongoDB\Field(name="attachment_mimetype", type="string", length=255, nullable=true)
     */
    protected $attachmentMimeType;

    /**
     * @var \DateTime
     *
     * @MongoDB\Field(name="created_at", type="timestamp")
     */
    protected $createdAt;


    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return TicketInterface
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param TicketInterface $ticket
     *
     * @return $this
     */
    public function setTicket(TicketInterface $ticket = null)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user.
     *
     * @param int|UserInterface $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        if (\is_object($user)) {
            $this->userObject = $user;
            $this->user = $user->getId();
        } else {
            $this->userObject = null;
            $this->user = $user;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserObject()
    {
        return $this->userObject;
    }

    /**
     * @param mixed $userObject
     *
     * @return $this
     */
    public function setUserObject($userObject)
    {
        $this->userObject = $userObject;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
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
        $status = \array_search(\strtolower($status), TicketMessageInterface::STATUSES);

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
        if (array_key_exists($this->status, TicketMessageInterface::STATUSES)) {
            return TicketMessageInterface::STATUSES[$this->status];
        }

        return TicketMessageInterface::STATUSES[0];
    }

    /**
     * Set priority.
     *
     * @param int $priority
     *
     * @return $this
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
     *
     * @return $this
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
        if (array_key_exists($this->priority, TicketMessageInterface::PRIORITIES)) {
            return TicketMessageInterface::PRIORITIES[$this->priority];
        }

        return TicketMessageInterface::PRIORITIES[0];
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachmentFile(File $file = null)
    {
        $this->attachmentFile = $file;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachmentFile()
    {
        return $this->attachmentFile;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachmentName($name)
    {
        $this->attachmentName = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachmentName()
    {
        return $this->attachmentName;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachmentSize($size)
    {
        $this->attachmentSize = $size;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachmentSize()
    {
        return $this->attachmentSize;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachmentMimeType($mimeType)
    {
        $this->attachmentMimeType =  $mimeType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachmentMimeType()
    {
        return $this->attachmentMimeType;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
