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

interface TicketInterface
{
    /**
     * Get ticket id.
     */
    public function getId();

    /**
     * Set status.
     *
     * @return $this
     */
    public function setStatus(int $status): self;

    /**
     * Set ticket status by string.
     *
     * @return $this
     */
    public function setStatusString(string $status): self;

    /**
     * Get ticket status.
     */
    public function getStatus(): ?int;

    /**
     * Get ticket status string.
     */
    public function getStatusString(): ?string;

    /**
     * Set ticket priority.
     *
     * @return $this
     */
    public function setPriority(int $priority): self;

    /**
     * Set ticket priority string.
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
     * Set userCreated.
     *
     *
     * @return $this
     */
    public function setUserCreated(?UserInterface $userCreated): self;

    /**
     * Get userCreated.
     */
    public function getUserCreated(): ?UserInterface;

    /**
     * Set lastUser.
     *
     *
     * @return $this
     */
    public function setLastUser(?UserInterface $lastUser): self;

    /**
     * Get lastUser .
     */
    public function getLastUser(): ?UserInterface;

    /**
     * Set lastMessage.
     *
     * @return $this
     */
    public function setLastMessage(DateTimeInterface $lastMessage): self;

    /**
     * Get lastMessage.
     */
    public function getLastMessage(): ?DateTimeInterface;

    /**
     * Set createdAt.
     *
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self;

    /**
     * Get createdAt.
     */
    public function getCreatedAt(): ?DateTimeInterface;

    /**
     * Set subject.
     *
     * @return $this
     */
    public function setSubject(string $subject): self;

    /**
     * Get ticket subject.
     */
    public function getSubject(): ?string;

    /**
     * Add message.
     *
     * @return $this
     */
    public function addMessage(TicketMessageInterface $message): self;

    /**
     * Remove message.
     */
    public function removeMessage(TicketMessageInterface $message);

    /**
     * Get messages.
     */
    public function getMessages(): Collection;
}
