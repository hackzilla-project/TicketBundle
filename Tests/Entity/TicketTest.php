<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new \Hackzilla\Bundle\TicketBundle\Entity\Ticket();
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
