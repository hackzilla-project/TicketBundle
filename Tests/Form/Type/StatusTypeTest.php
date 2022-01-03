<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Hackzilla\Bundle\TicketBundle\Form\Type\StatusType;
use Symfony\Component\Form\Test\TypeTestCase;

class StatusTypeTest extends TypeTestCase
{
    private $object;

    public function setUp(): void
    {
        $this->object = new StatusType();
    }

    public function tearDown(): void
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(StatusType::class, $this->object);
    }
}
