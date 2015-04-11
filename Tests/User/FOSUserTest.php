<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FOSUserTest extends WebTestCase
{
    private $_object;

    public function setUp()
    {
        $this->_object = new \Hackzilla\Bundle\TicketBundle\User\FOSUser($this->getMockSecurity(), $this->getMockUserManager());
    }

    public function tearDown()
    {
        unset($this->_object);
    }

    public function testObjectCreated()
    {
        $this->assertTrue(\is_object($this->_object));
    }

    private function getMockSecurity()
    {
        $security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');

        return $security;
    }

    private function getMockUserManager()
    {
        $userManager = $this->getMock('FOS\UserBundle\Model\UserManagerInterface');

        return $userManager;
    }
}

