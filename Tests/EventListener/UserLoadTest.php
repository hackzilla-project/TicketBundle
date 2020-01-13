<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\EventListener;

use Hackzilla\Bundle\TicketBundle\EventListener\UserLoad;
use Hackzilla\Bundle\TicketBundle\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserLoadTest extends WebTestCase
{
    private $object;

    public function setUp(): void
    {
        $userManager = $this->getUserManagerMock();

        $this->object = new UserLoad($userManager);
    }

    public function getUserManagerMock()
    {
        return $this->createMock(UserManager::class);
    }

    public function tearDown(): void
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(UserLoad::class, $this->object);
    }
}
