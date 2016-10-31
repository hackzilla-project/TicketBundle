<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\DependencyInjection;

use Hackzilla\Bundle\TicketBundle\DependencyInjection\HackzillaTicketExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HackzillaTicketExtensionTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new HackzillaTicketExtension();
    }

    public function tearDown()
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(HackzillaTicketExtension::class, $this->object);
    }
}
