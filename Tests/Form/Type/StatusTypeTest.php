<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;

class StatusTypeTest extends TypeTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new \Hackzilla\Bundle\TicketBundle\Form\Type\StatusType();
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
