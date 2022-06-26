<?php

declare(strict_types=1);

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Tests\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Hackzilla\Bundle\TicketBundle\Manager\UserManager;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class UserManagerTest extends WebTestCase
{
    private $object;

    private $tokenStorage;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->tokenStorage = new TokenStorage();

        $this->object = new UserManager(
            $this->tokenStorage,
            $this->getMockUserRepository(),
            $this->getAuthorizationChecker(),
        );
    }

    protected function tearDown(): void
    {
        $this->object = null;
    }

    public function testObjectCreated(): void
    {
        $this->assertInstanceOf(UserManager::class, $this->object);
    }

    private function getMockUserRepository(): EntityRepository
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);

        return new EntityRepository($em, new ClassMetadata(User::class));
    }

    private function getAuthorizationChecker()
    {
        return $this->createMock(AuthorizationChecker::class);
    }
}
