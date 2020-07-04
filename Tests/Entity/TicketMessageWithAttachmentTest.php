<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Entity;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessageWithAttachment;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TicketMessageWithAttachmentTest extends WebTestCase
{
    private $object;

    protected function setUp()
    {
        $this->object = new TicketMessageWithAttachment();
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(TicketMessageWithAttachment::class, $this->object);
    }
}
