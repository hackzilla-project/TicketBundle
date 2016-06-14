<?php

namespace Hackzilla\Bundle\TicketBundle\Model;

interface TicketInterface
{
    /**
     * Get ticket id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * Set ticket status by string.
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatusString($status);

    /**
     * Get ticket status.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get ticket status string.
     *
     * @return string
     */
    public function getStatusString();

    /**
     * Set ticket priority.
     *
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Set ticket priority string.
     *
     * @return $this
     */
    public function setPriorityString($priority);

    /**
     * Get priority.
     *
     * @return int
     */
    public function getPriority();

    /**
     * Get priority string.
     *
     * @return string
     */
    public function getPriorityString();

    /**
     * Set userCreated.
     *
     * @param int|UserInterface $userCreated
     *
     * @return $this
     */
    public function setUserCreated($userCreated);

    /**
     * Get userCreated.
     *
     * @return int
     */
    public function getUserCreated();

    /**
     * Get userCreated object.
     *
     * @return UserInterface
     */
    public function getUserCreatedObject();

    /**
     * Set lastUser.
     *
     * @param int|UserInterface $lastUser
     *
     * @return $this
     */
    public function setLastUser($lastUser);

    /**
     * Get lastUser id.
     *
     * @return mixed
     */
    public function getLastUser();

    /**
     * Get lastUser object.
     *
     * @return UserInterface
     */
    public function getLastUserObject();

    /**
     * Set lastMessage.
     *
     * @param \DateTime $lastMessage
     *
     * @return $this
     */
    public function setLastMessage($lastMessage);

    /**
     * Get lastMessage.
     *
     * @return \DateTime
     */
    public function getLastMessage();

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set subject.
     *
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject);

    /**
     * Get ticket subject.
     *
     * @return string
     */
    public function getSubject();

    /**
     * Add message.
     *
     * @param TicketMessageInterface $message
     *
     * @return $this
     */
    public function addMessage(TicketMessageInterface $message);

    /**
     * Remove message.
     *
     * @param TicketMessageInterface $message
     */
    public function removeMessage(TicketMessageInterface $message);

    /**
     * Get messages.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages();
}
