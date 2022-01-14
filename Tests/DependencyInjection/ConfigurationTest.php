<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\DependencyInjection;

use Hackzilla\Bundle\TicketBundle\DependencyInjection\Configuration;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ConfigurationTest extends WebTestCase
{
    private $object;

    protected function setUp(): void
    {
        $this->object = new Configuration();
    }

    protected function tearDown(): void
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(Configuration::class, $this->object);
    }
}
