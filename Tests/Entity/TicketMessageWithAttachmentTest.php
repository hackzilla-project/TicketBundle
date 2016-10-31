<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Entity;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessageWithAttachment;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketMessageWithAttachmentTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new TicketMessageWithAttachment();
    }

    public function tearDown()
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(TicketMessageWithAttachment::class, $this->object);
    }
}
