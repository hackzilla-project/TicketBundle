<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserLoadTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $container = $this->getMockContainer();

        $this->object = new \Hackzilla\Bundle\TicketBundle\EventListener\UserLoad($container);
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
