<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketMessageTypeTest extends WebTestCase
{
    private $_object;

    public function setUp()
    {
        $userManager = $this->getMock('Hackzilla\Interfaces\User\UserInterfaces');
        $this->assertTrue($userManager instanceof \Hackzilla\Interfaces\User\UserInterfaces);

        $this->_object = new \Hackzilla\Bundle\TicketBundle\Form\TicketMessageType($userManager);
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
