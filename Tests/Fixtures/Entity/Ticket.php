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
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $user_created_id;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $last_user_id;

    #[ORM\Column(type: 'text', nullable: false)]
    private $last_message;

    #[ORM\Column(type: 'text', nullable: false)]
    private $subject;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $status;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $priority;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: TicketMessage::class, inversedBy: 'ticket')]
    #[ORM\JoinColumn(nullable: false)]
    private $messages;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
