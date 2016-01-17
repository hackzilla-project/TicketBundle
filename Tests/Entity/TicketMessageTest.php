<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketMessageTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new \Hackzilla\Bundle\TicketBundle\Entity\TicketMessage();
    }

    public function tearDown()
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertTrue(\is_object($this->object));
    }
}
