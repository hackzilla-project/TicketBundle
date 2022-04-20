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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageTrait;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 * @author Daniel Platt <github@ofdan.co.uk>
 */
#[ORM\Entity()]
class TicketMessage implements TicketMessageInterface
{
    use TicketMessageTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $message;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $status;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $priority;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private $ticket;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $user;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
