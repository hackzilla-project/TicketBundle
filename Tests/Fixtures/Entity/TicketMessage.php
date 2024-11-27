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
use Doctrine\ORM\Mapping as ORM;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageTrait;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 * @author Daniel Platt <github@ofdan.co.uk>
 */
#[ORM\Entity]
class TicketMessage implements TicketMessageInterface
{
    use TicketMessageTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $status;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $priority;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private \DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
