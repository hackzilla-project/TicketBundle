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

interface TicketMessageInterface
{
    public const STATUS_INVALID = 0;

    public const STATUS_OPEN = 10;

    public const STATUS_IN_PROGRESS = 11;

    public const STATUS_INFORMATION_REQUESTED = 12;

    public const STATUS_ON_HOLD = 13;

    public const STATUS_RESOLVED = 14;

    public const STATUS_CLOSED = 15;

    public const STATUSES = [
        self::STATUS_INVALID => 'STATUS_INVALID',
        self::STATUS_OPEN => 'STATUS_OPEN',
        self::STATUS_IN_PROGRESS => 'STATUS_IN_PROGRESS',
        self::STATUS_INFORMATION_REQUESTED => 'STATUS_INFORMATION_REQUESTED',
        self::STATUS_ON_HOLD => 'STATUS_ON_HOLD',
        self::STATUS_RESOLVED => 'STATUS_RESOLVED',
        self::STATUS_CLOSED => 'STATUS_CLOSED',
    ];

    public const PRIORITY_INVALID = 0;

    public const PRIORITY_LOW = 20;

    public const PRIORITY_MEDIUM = 21;

    public const PRIORITY_HIGH = 22;

    public const PRIORITIES = [
        self::PRIORITY_INVALID => 'PRIORITY_INVALID',
        self::PRIORITY_LOW => 'PRIORITY_LOW',
        self::PRIORITY_MEDIUM => 'PRIORITY_MEDIUM',
        self::PRIORITY_HIGH => 'PRIORITY_HIGH',
    ];

    /**
     * Get id.
     */
    public function getId();

    /**
     * Set status.
     *
     * @return $this
     */
    public function setStatus(int $status): self;

    /**
     * Set status string.
     *
     * @return $this
     */
    public function setStatusString(string $status): self;

    /**
     * Get status.
     */
    public function getStatus(): ?int;

    /**
     * Get status string.
     */
    public function getStatusString(): ?string;

    /**
     * Set priority.
     *
     * @return $this
     */
    public function setPriority(int $priority): self;

    /**
     * Set priority string.
     *
     * @return $this
     */
    public function setPriorityString(string $priority): self;

    /**
     * Get priority.
     */
    public function getPriority(): ?int;

    /**
     * Get priority string.
     */
    public function getPriorityString(): ?string;

    /**
     * Set user.
     *
     * @return $this
     */
    public function setUser(?UserInterface $user): self;

    /**
     * Get user.
     */
    public function getUser(): ?UserInterface;

    /**
     * Set message.
     *
     * @return $this
     */
    public function setMessage(string $message): self;

    /**
     * Get message.
     */
    public function getMessage(): ?string;

    /**
     * Set createdAt.
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt): self;

    /**
     * Get createdAt.
     */
    public function getCreatedAt(): \DateTime;

    /**
     * Set ticket.
     *
     * @return $this
     */
    public function setTicket(?TicketInterface $ticket = null): self;

    /**
     * Get ticket.
     */
    public function getTicket(): ?TicketInterface;
}
