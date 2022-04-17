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

namespace Hackzilla\Bundle\TicketBundle\Model;

use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait PermissionsServiceTrait
{
    /**
     * used in TicketManager::getTicketListQuery().
     *
     * @param object $query
     */
    public function addUserPermissionsCondition($query, UserInterface|string $user, UserManagerInterface $userManager)
    {
        if (\is_object($user)) {
            if (!$userManager->hasRole($user, TicketRole::ADMIN)) {
                $query
                    ->andWhere('t.userCreatedObject = :user')
                    ->setParameter('user', $user);
            }
        } else {
            // anonymous user
            $query
                ->andWhere('t.userCreated = :userId')
                ->setParameter('userId', 0);
        }

        return $query;
    }

    /**
     * used by UserManager::hasPermission().
     *
     * @param UserInterface|string $user
     */
    public function hasPermission($user, TicketInterface $ticket, UserManagerInterface $userManager): void
    {
        if (!\is_object($user) || (!$userManager->hasRole($user, TicketRole::ADMIN) &&
                (null === $ticket->getUserCreatedObject() || $ticket->getUserCreatedObject()->getId() != $user->getId()))
        ) {
            throw new AccessDeniedHttpException();
        }
    }
}
