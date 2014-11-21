<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class StatusTypeTest extends TypeTestCase
{
    private $_object;

    public function setUp()
    {
        $this->_object = new \Hackzilla\Bundle\TicketBundle\Form\Type\StatusType();
    }

    public function tearDown()
    {
        unset($this->_object);
    }

    public function testObjectCreated()
    {
        $this->assertTrue(\is_object($this->_object));
    }
}
