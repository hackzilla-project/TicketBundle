<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\User;

use Doctrine\ORM\EntityRepository;
use Hackzilla\Bundle\TicketBundle\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class UserManagerTest extends WebTestCase
{
    private $object;

    private $tokenStorage;

    private $authorizationChecker;

    public function setUp(): void
    {
        $this->tokenStorage            = new TokenStorage();
        $authenticationProviderManager = new AuthenticationProviderManager([new AnonymousAuthenticationProvider('secret')]);
        $accessDecisionManager         = new AccessDecisionManager();
        $this->authorizationChecker    = new AuthorizationChecker($this->tokenStorage, $authenticationProviderManager, $accessDecisionManager);

        $this->object = new UserManager(
            $this->tokenStorage,
            $this->getMockUserRepository(),
            $this->authorizationChecker
        );
    }

    private function getMockUserRepository()
    {
        return $this->createMock(EntityRepository::class);
    }

    public function tearDown(): void
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(UserManager::class, $this->object);
    }
}
