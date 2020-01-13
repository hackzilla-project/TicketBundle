<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Entity;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketMessageTest extends WebTestCase
{
    private $object;

    public function setUp(): void
    {
        $this->object = new TicketMessage();
    }

    public function tearDown(): void
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(TicketMessage::class, $this->object);
    }
}
