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

use DateTime;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\TicketMessageWithAttachment;

/**
 * Ticket Message Trait.
 */
trait TicketMessageTrait
{
    /**
     * Set status.
     *
     * @param int $status
     *
     * @return TicketMessage|TicketMessageTrait|TicketMessageWithAttachment
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
     * @return TicketMessage|TicketMessageTrait|TicketMessageWithAttachment
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
        if (isset(TicketMessageInterface::STATUSES[$this->status]) && (TicketMessageInterface::STATUSES[$this->status] !== '' && TicketMessageInterface::STATUSES[$this->status] !== '0')) {
            return TicketMessageInterface::STATUSES[$this->status];
        }

        return TicketMessageInterface::STATUSES[0];
    }

    /**
     * Set priority.
     *
     * @param int $priority
     *
     * @return TicketMessage|TicketMessageTrait|TicketMessageWithAttachment
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
     * @return TicketMessage|TicketMessageTrait|TicketMessageWithAttachment
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
        if (isset(TicketMessageInterface::PRIORITIES[$this->priority]) && (TicketMessageInterface::PRIORITIES[$this->priority] !== '' && TicketMessageInterface::PRIORITIES[$this->priority] !== '0')) {
            return TicketMessageInterface::PRIORITIES[$this->priority];
        }

        return TicketMessageInterface::PRIORITIES[0];
    }

    /**
     * Set user.
     *
     * @param ?UserInterface $user
     *
     * @return TicketMessage|TicketMessageTrait|TicketMessageWithAttachment
     */
    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return TicketMessage|TicketMessageTrait|TicketMessageWithAttachment
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set createdAt.
     *
     * @param DateTime $createdAt
     *
     * @return TicketMessage|TicketMessageTrait|TicketMessageWithAttachment
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set ticket.
     *
     * @param TicketInterface|null $ticket
     *
     * @return TicketMessage|TicketMessageTrait|TicketMessageWithAttachment
     */
    public function setTicket(?TicketInterface $ticket = null): self
    {
        $this->ticket = $ticket;
        $user = $this->getUser();

        // if null, then new ticket
        if (!$ticket->getUserCreated() instanceof UserInterface) {
            $ticket->setUserCreated($user);
        }

        $ticket->setLastUser($user);
        $ticket->setLastMessage($this->getCreatedAt());
        $ticket->setPriority($this->getPriority());

        // if ticket not closed, then it'll be set to null
        if (null === $this->getStatus()) {
            $this->setStatus($ticket->getStatus());
        } else {
            $ticket->setStatus($this->getStatus());
        }

        return $this;
    }

    /**
     * Get ticket.
     */
    public function getTicket(): ?TicketInterface
    {
        return $this->ticket;
    }
}
