<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class PriorityTypeTest extends TypeTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new \Hackzilla\Bundle\TicketBundle\Form\Type\PriorityType();
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
