<?php

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Tests\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Hackzilla\Bundle\TicketBundle\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\EventListener\UserLoad;
use Hackzilla\Bundle\TicketBundle\Manager\UserManager;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;
use Hackzilla\Bundle\TicketBundle\Tests\Functional\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserLoadTest extends WebTestCase
{
    private const USER_CREATED_ID = 42;
    private const LAST_USER_ID = 43;
    private const USER_MESSAGE_ID = 44;

    /**
     * @var UserLoad
     */
    private $userLoad;

    /**
     * @var UserInterface
     */
    private $userCreated;

    /**
     * @var UserInterface
     */
    private $lastUser;

    /**
     * @var UserInterface
     */
    private $userMessage;

    protected function setUp(): void
    {
        $this->userCreated = new User();
        $this->lastUser = new User();
        $this->userMessage = new User();

        (\Closure::bind(function ($id): void {
            $this->id = $id;
        }, $this->userCreated, User::class))(self::USER_CREATED_ID);

        (\Closure::bind(function ($id): void {
            $this->id = $id;
        }, $this->lastUser, User::class))(self::LAST_USER_ID);

        (\Closure::bind(function ($id): void {
            $this->id = $id;
        }, $this->userMessage, User::class))(self::USER_MESSAGE_ID);

        $userManager = $this->getUserManagerMock();

        $this->userLoad = new UserLoad($userManager);
    }

    protected function tearDown(): void
    {
        $this->userCreated = null;
        $this->lastUser = null;
        $this->userMessage = null;
        $this->userLoad = null;
    }

    public function testObjectCreated(): void
    {
        $this->assertInstanceOf(UserLoad::class, $this->userLoad);
    }

    public function testPostLoad(): void
    {
        $objectManager = $this->createStub(ObjectManager::class);

        $ticket = new Ticket();
        $ticket->setUserCreated(self::USER_CREATED_ID);
        $ticket->setLastUser(self::LAST_USER_ID);

        $this->assertNull($ticket->getUserCreatedObject());
        $this->assertNull($ticket->getLastUserObject());

        $this->userLoad->postLoad(new LifecycleEventArgs($ticket, $objectManager));

        $this->assertSame($this->userCreated, $ticket->getUserCreatedObject());
        $this->assertSame($this->lastUser, $ticket->getLastUserObject());

        $message = new TicketMessage();
        $message->setUser(self::USER_MESSAGE_ID);

        $this->assertNull($message->getUserObject());

        $this->userLoad->postLoad(new LifecycleEventArgs($message, $objectManager));

        $this->assertSame($this->userMessage, $message->getUserObject());
    }

    private function getUserManagerMock(): MockObject
    {
        $userManager = $this->createMock(UserManager::class);
        $userManager
            ->method('getUserById')
            ->willReturnCallback(function ($userId): ?UserInterface {
                if ($userId === $this->userCreated->getId()) {
                    return $this->userCreated;
                }

                if ($userId === $this->lastUser->getId()) {
                    return $this->lastUser;
                }

                if ($userId === $this->userMessage->getId()) {
                    return $this->userMessage;
                }

                return null;
            });

        return $userManager;
    }
}
