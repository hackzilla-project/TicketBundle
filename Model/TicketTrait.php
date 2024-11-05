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

use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\Ticket;
use function array_key_exists;

/**
 * Ticket Trait.
 */
trait TicketTrait
{
    /**
     * Set status.
     *
     * @param int $status
     *
     * @return Ticket|TicketTrait
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set status string.
     *
     * @param string $status
     *
     * @return Ticket|TicketTrait
     */
    public function setStatusString(string $status): self
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
     * @return Ticket|TicketTrait
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Set priority string.
     *
     * @param string $priority
     *
     * @return Ticket|TicketTrait
     */
    public function setPriorityString(string $priority): self
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
        if (array_key_exists($this->priority, TicketMessageInterface::PRIORITIES)) {
            return TicketMessageInterface::PRIORITIES[$this->priority];
        }

        return TicketMessageInterface::PRIORITIES[0];
    }

    /**
     * Set userCreated.
     *
     *
     * @param UserInterface|null $userCreated
     *
     * @return Ticket|TicketTrait
     */
    public function setUserCreated(?UserInterface $userCreated): self
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    /**
     * Get userCreated.
     */
    public function getUserCreated(): ?UserInterface
    {
        return $this->userCreated;
    }

    /**
     * Set lastUser.
     *
     *
     * @param UserInterface|null $lastUser
     *
     * @return Ticket|TicketTrait
     */
    public function setLastUser(?UserInterface $lastUser): self
    {
        $this->lastUser = $lastUser;

        return $this;
    }

    /**
     * Get lastUser.
     */
    public function getLastUser(): ?UserInterface
    {
        return $this->lastUser;
    }

    /**
     * Set lastMessage.
     *
     * @param DateTimeInterface $lastMessage
     *
     * @return Ticket|TicketTrait
     */
    public function setLastMessage(DateTimeInterface $lastMessage): self
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * Get lastMessage.
     */
    public function getLastMessage(): ?DateTimeInterface
    {
        return $this->lastMessage;
    }

    /**
     * Set createdAt.
     *
     * @param DateTimeInterface $createdAt
     *
     * @return Ticket|TicketTrait
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set subject.
     *
     * @param string $subject
     *
     * @return Ticket|TicketTrait
     */
    public function setSubject(string $subject): self
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
     * @param TicketMessageInterface $message
     *
     * @return Ticket|TicketTrait
     */
    public function addMessage(TicketMessageInterface $message): self
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message.
     *
     * @param TicketMessageInterface $message
     *
     * @return Ticket|TicketTrait
     */
    public function removeMessage(TicketMessageInterface $message): self
    {
        $this->messages->removeElement($message);

        return $this;
    }

    /**
     * Get messages.
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }
}
