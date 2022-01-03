<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Hackzilla\Bundle\TicketBundle\Form\Type\PriorityType;
use Symfony\Component\Form\Test\TypeTestCase;

class PriorityTypeTest extends TypeTestCase
{
    private $object;

    public function setUp(): void
    {
        $this->object = new PriorityType();
    }

    public function tearDown(): void
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(PriorityType::class, $this->object);
    }
}
