<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\User;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class UserManagerTest extends WebTestCase
{
    private $object;
    private $tokenStorage;

    public function setUp()
    {
        $this->tokenStorage = new TokenStorage();

        $this->object = new \Hackzilla\Bundle\TicketBundle\Manager\UserManager(
            $this->getMockAuthorizationChecker(),
            $this->tokenStorage,
            $this->getMockUserRepository()
        );
    }

    private function getMockAuthorizationChecker()
    {
        return $this
            ->getMockBuilder(AuthorizationChecker::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getMockUserRepository()
    {
        $userRepository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $userRepository;
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
