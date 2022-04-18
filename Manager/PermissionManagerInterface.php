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
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;

interface PermissionManagerInterface
{
    public function setUserManager(UserManagerInterface $userManager): void;

    /**
     * used in TicketManager::getTicketListQuery().
     *
     * @param object $query
     */
    public function addUserPermissionsCondition($query, UserInterface $user);

    /**
     * used by UserManager::hasPermission().
     *
     * @param ?UserInterface $user
     */
    public function hasPermission(?UserInterface $user, TicketInterface $ticket): void;
}
