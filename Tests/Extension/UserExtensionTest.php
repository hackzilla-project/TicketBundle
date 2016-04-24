<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Extension;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserExtensionTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $userManager = $this->getMockUserManager();

        $this->object = new \Hackzilla\Bundle\TicketBundle\Extension\UserExtension($userManager);
    }

    public function getMockUserManager()
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
