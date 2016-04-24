<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\EventListener;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserLoadTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $userManager = $this->getUserManagerMock();

        $this->object = new \Hackzilla\Bundle\TicketBundle\EventListener\UserLoad($userManager);
    }

    public function getUserManagerMock()
    {
        return $this
            ->getMockBuilder('Hackzilla\Bundle\TicketBundle\Manager\UserManager')
            ->disableOriginalConstructor()
            ->getMock();
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
