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

namespace Hackzilla\Bundle\TicketBundle\Tests\Manager;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectManager;
use Hackzilla\Bundle\TicketBundle\Manager\PermissionManager;
use Hackzilla\Bundle\TicketBundle\Manager\TicketManager;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\TicketMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class TicketManagerTest extends WebTestCase
{
    public function testGetTicketListQuery(): void
    {
        $ticketClass = Ticket::class;
        $ticketMessageClass = TicketMessage::class;

        $qb = $this->createMock(QueryBuilder::class);
        $qb
            ->method('orderBy')
            ->willReturn($qb);
        $qb
            ->method('andWhere')
            ->willReturn($qb);
        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $om = $this->createMock(ObjectManager::class);
        $om
            ->method('getRepository')
            ->willReturn($entityRepository);

        $permissionManager = $this->createMock(PermissionManager::class);

        $userManager = $this->createMock(UserManagerInterface::class);
        $userManager
            ->method('getCurrentUser')
            ->willReturn(new QueryBuilder($om));

        $ticketManager = (new TicketManager($ticketClass, $ticketMessageClass))
            ->setObjectManager($om)
            ->setUserManager($userManager)
        ;

        $this->assertInstanceOf(QueryBuilder::class, $ticketManager->getTicketListQuery(TicketMessageInterface::STATUS_OPEN));
    }
}
