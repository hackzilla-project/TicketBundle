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

namespace Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity;

use Doctrine\DBAL\Types\Types;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketTrait;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 * @author Daniel Platt <github@ofdan.co.uk>
 */
#[ORM\Entity()]
class Ticket implements TicketInterface
{
    use TicketTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private DateTimeInterface $lastMessage;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private string $subject;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $status;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $priority;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private DateTimeInterface $createdAt;

    /**
     * @var Collection<int, TicketMessage>
     */
    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: TicketMessage::class)]
    private Collection $messages;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $userCreated = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $lastUser = null;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
