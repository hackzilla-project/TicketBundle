<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\DependencyInjection;

use Hackzilla\Bundle\TicketBundle\DependencyInjection\Configuration;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfigurationTest extends WebTestCase
{
    private $object;

    public function setUp(): void
    {
        $this->object = new Configuration();
    }

    public function tearDown(): void
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(Configuration::class, $this->object);
    }
}
