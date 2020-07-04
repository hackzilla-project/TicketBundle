<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\EventListener;

use Hackzilla\Bundle\TicketBundle\EventListener\UserLoad;
use Hackzilla\Bundle\TicketBundle\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserLoadTest extends WebTestCase
{
    private $object;

    protected function setUp()
    {
        $userManager = $this->getUserManagerMock();

        $this->object = new UserLoad($userManager);
    }

    protected function tearDown()
    {
        unset($this->object);
    }

    public function getUserManagerMock()
    {
        return $this->createMock(UserManager::class);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(UserLoad::class, $this->object);
    }
}
