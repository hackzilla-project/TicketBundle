<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\EventListener;

use Hackzilla\Bundle\TicketBundle\Manager\UserManager;
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
            ->getMockBuilder(UserManager::class)
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
