<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Entity;

use Hackzilla\Bundle\TicketBundle\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new Ticket();
    }

    public function tearDown()
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(Ticket::class, $this->object);
    }

    public function testStatus()
    {
        $this->object->setStatus(TicketMessageInterface::STATUS_INVALID);
        $this->assertSame(TicketMessageInterface::STATUS_INVALID, $this->object->getStatus());
    }
}
