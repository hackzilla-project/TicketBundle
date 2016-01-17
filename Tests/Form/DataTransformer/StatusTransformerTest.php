<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\DataTransformer;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatusTransformerTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new \Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer();
    }

    public function tearDown()
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertTrue(\is_object($this->object));
    }

    public function testTransform()
    {
        $this->assertSame($this->object->transform(TicketMessage::STATUS_CLOSED), 1);

        $this->assertNull($this->object->transform('TEST'));
    }

    public function testReverseTransform()
    {
        $this->assertSame($this->object->reverseTransform(1), TicketMessage::STATUS_CLOSED);

        $this->assertNull($this->object->reverseTransform('TEST'));
    }
}
