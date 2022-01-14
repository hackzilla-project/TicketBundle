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

namespace Hackzilla\Bundle\TicketBundle\Tests\EventListener;

use Hackzilla\Bundle\TicketBundle\EventListener\UserLoad;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserLoadTest extends WebTestCase
{
    private $object;

    protected function setUp(): void
    {
        $userManager = $this->getUserManagerMock();

        $this->object = new UserLoad($userManager);
    }

    protected function tearDown(): void
    {
        $this->object = null;
    }

    public function getUserManagerMock(): UserManagerInterface
    {
        $userManager = $this->getMockBuilder(UserManagerInterface::class)
            ->getMock();

        $user = new User();

        $userManager
            ->method('getUserById')
            ->willReturn($user);

        return $userManager;
    }

    public function testObjectCreated(): void
    {
        $this->assertInstanceOf(UserLoad::class, $this->object);
    }
}
