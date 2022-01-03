<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\DataTransformer;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatusTransformerTest extends WebTestCase
{
    private $object;

    public function setUp(): void
    {
        $this->object = new StatusTransformer();
    }

    public function tearDown(): void
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(StatusTransformer::class, $this->object);
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
