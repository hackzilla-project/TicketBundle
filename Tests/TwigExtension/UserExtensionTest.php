<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\TwigExtension;

use Hackzilla\Bundle\TicketBundle\TwigExtension\UserExtension;
use Hackzilla\Bundle\TicketBundle\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserExtensionTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $userManager = $this->getMockUserManager();

        $this->object = new UserExtension($userManager);
    }

    public function getMockUserManager()
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
        $this->assertInstanceOf(UserExtension::class, $this->object);
    }
}
