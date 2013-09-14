<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Extension;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserExtensionTest extends WebTestCase
{
    private $_object;

    public function setUp()
    {
        $container = $this->getMockContainer();

        $this->_object = new \Hackzilla\Bundle\TicketBundle\Extension\UserExtension($container);
    }

    public function getMockContainer()
    {
        return $this->getMock('Symfony\Component\DependencyInjection\Container');
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
