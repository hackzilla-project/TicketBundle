<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketTypeTest extends WebTestCase
{
    private $_object;

    public function setUp()
    {
        $userManager = $this->getMock('Hackzilla\Interfaces\User\UserInterface');
        $this->assertTrue($userManager instanceof \Hackzilla\Interfaces\User\UserInterface);

        $this->_object = new \Hackzilla\Bundle\TicketBundle\Form\TicketType($userManager);
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
