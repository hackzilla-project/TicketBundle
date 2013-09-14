<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketMessageTypeTest extends WebTestCase
{
    private $_object;

    public function setUp()
    {
        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');

        $this->_object = new \Hackzilla\Bundle\TicketBundle\Form\TicketMessageType($securityContext);
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
