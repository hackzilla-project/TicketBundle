<?php

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Tests\Manager;

use Doctrine\ORM\EntityRepository;
use Hackzilla\Bundle\TicketBundle\Manager\UserManager;
use Hackzilla\Bundle\TicketBundle\Tests\Functional\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

final class UserManagerTest extends WebTestCase
{
    private $object;

    private $tokenStorage;

    private $authorizationChecker;

    protected function setUp(): void
    {
        $this->tokenStorage = new TokenStorage();
        $authenticationProviderManager = new AuthenticationProviderManager([new AnonymousAuthenticationProvider('secret')]);
        $accessDecisionManager = new AccessDecisionManager();
        $this->authorizationChecker = new AuthorizationChecker($this->tokenStorage, $authenticationProviderManager, $accessDecisionManager);

        $this->object = new UserManager(
            $this->tokenStorage,
            $this->getMockUserRepository(),
            $this->authorizationChecker
        );
    }

    protected function tearDown(): void
    {
        $this->object = null;
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(UserManager::class, $this->object);
    }

    private function getMockUserRepository()
    {
        $userRepository = $this->createMock(EntityRepository::class);
        $userRepository
            ->method('getClassName')
            ->willReturn(User::class);

        return $userRepository;
    }
}
