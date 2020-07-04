<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Entity;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TicketMessageTest extends WebTestCase
{
    private $object;

    protected function setUp()
    {
        $this->object = new TicketMessage();
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(TicketMessage::class, $this->object);
    }
}
