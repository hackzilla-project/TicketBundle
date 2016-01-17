<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Extension;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserExtensionTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $container = $this->getMockContainer();

        $this->object = new \Hackzilla\Bundle\TicketBundle\Extension\UserExtension($container);
    }

    public function getMockContainer()
    {
        return $this->getMock('Symfony\Component\DependencyInjection\Container');
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
