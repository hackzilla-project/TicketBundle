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

namespace Hackzilla\Bundle\TicketBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Ticket Trait.
 */
trait TicketTrait
{
    /**
     * Set status.
     *
     * @return $this
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set status string.
     *
     * @return $this
     */
    public function setStatusString(string $status)
    {
        $status = array_search(strtolower($status), TicketMessageInterface::STATUSES, true);

        if ($status > 0) {
            $this->setStatus($status);
        }

        return $this;
    }

    /**
     * Get status.
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * Get status string.
     */
    public function getStatusString(): ?string
    {
        if (\array_key_exists($this->status, TicketMessageInterface::STATUSES)) {
            return TicketMessageInterface::STATUSES[$this->status];
        }

        return TicketMessageInterface::STATUSES[0];
    }

    /**
     * Set priority.
     *
     * @return $this
     */
    public function setPriority(int $priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Set priority string.
     *
     * @return $this
     */
    public function setPriorityString(string $priority)
    {
        $priority = array_search(strtolower($priority), TicketMessageInterface::PRIORITIES, true);

        if ($priority > 0) {
            $this->setPriority($priority);
        }

        return $this;
    }

    /**
     * Get priority.
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * Get priority string.
     */
    public function getPriorityString(): ?string
    {
        if (\array_key_exists($this->priority, TicketMessageInterface::PRIORITIES)) {
            return TicketMessageInterface::PRIORITIES[$this->priority];
        }

        return TicketMessageInterface::PRIORITIES[0];
    }

    /**
     * Set userCreated.
     *
     * @param ?UserInterface $userCreated
     *
     * @return $this
     */
    public function setUserCreated(?UserInterface $userCreated)
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    /**
     * Get userCreated.
     *
     * @return ?UserInterface
     */
    public function getUserCreated(): ?UserInterface
    {
        return $this->userCreated;
    }

    /**
     * Set lastUser.
     *
     * @param ?UserInterface $lastUser
     *
     * @return $this
     */
    public function setLastUser(?UserInterface $lastUser)
    {
        $this->lastUser = $lastUser;

        return $this;
    }

    /**
     * Get lastUser.
     *
     * @return ?UserInterface
     */
    public function getLastUser(): ?UserInterface
    {
        return $this->lastUser;
    }

    /**
     * Set lastMessage.
     *
     * @return $this
     */
    public function setLastMessage(\DateTimeInterface $lastMessage)
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * Get lastMessage.
     */
    public function getLastMessage(): ?\DateTimeInterface
    {
        return $this->lastMessage;
    }

    /**
     * Set createdAt.
     *
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set subject.
     *
     * @return $this
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject.
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * Add message.
     *
     * @return $this
     */
    public function addMessage(TicketMessageInterface $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message.
     *
     * @return $this
     */
    public function removeMessage(TicketMessageInterface $message)
    {
        $this->messages->removeElement($message);

        return $this;
    }

    /**
     * Get messages.
     */
    public function getMessages(): Collection
    {
        if (null === $this->messages) {
            $this->messages = new ArrayCollection();
        }

        return $this->messages;
    }
}
