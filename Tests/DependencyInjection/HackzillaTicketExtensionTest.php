<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HackzillaTicketExtensionTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new \Hackzilla\Bundle\TicketBundle\DependencyInjection\HackzillaTicketExtension();
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
