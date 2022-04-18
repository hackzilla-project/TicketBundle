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

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait PermissionManagerTrait
{
    private ?UserManagerInterface $userManager;

    public function setUserManager(UserManagerInterface $userManager): void
    {
        $this->userManager = $userManager;
    }

    /**
     * used in TicketManager::getTicketListQuery().
     *
     * @param object $query
     */
    public function addUserPermissionsCondition($query, ?UserInterface $user)
    {
        if (\is_object($user)) {
            if (!$this->getUserManager()->hasRole($user, TicketRole::ADMIN)) {
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
     * @param ?UserInterface $user
     */
    public function hasPermission(?UserInterface $user, TicketInterface $ticket): void
    {
        if (!\is_object($user) || (!$this->getUserManager()->hasRole($user, TicketRole::ADMIN) &&
                (null === $ticket->getUserCreatedObject() || $ticket->getUserCreatedObject()->getId() != $user->getId()))
        ) {
            throw new AccessDeniedHttpException();
        }
    }

    private function getUserManager(): UserManagerInterface
    {
        if (!$this->userManager) {
            throw new \Exception('Undefined UserManager');
        }

        return $this->userManager;
    }
}
