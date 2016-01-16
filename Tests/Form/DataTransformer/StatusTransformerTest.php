<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\DataTransformer;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatusTransformerTest extends WebTestCase
{
    private $_object;

    public function setUp()
    {
        $this->_object = new \Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer();
    }

    public function tearDown()
    {
        unset($this->_object);
    }

    public function testObjectCreated()
    {
        $this->assertTrue(\is_object($this->_object));
    }

    public function testTransform()
    {
        $this->assertSame($this->_object->transform(TicketMessage::STATUS_CLOSED), 1);

        $this->assertNull($this->_object->transform('TEST'));
    }

    public function testReverseTransform()
    {
        $this->assertSame($this->_object->reverseTransform(1), TicketMessage::STATUS_CLOSED);

        $this->assertNull($this->_object->reverseTransform('TEST'));
    }
}
