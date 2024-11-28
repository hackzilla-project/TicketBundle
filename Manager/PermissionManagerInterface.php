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

use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;

interface PermissionManagerInterface
{
    /**
     * used in TicketManager::getTicketListQuery().
     */
    public function addUserPermissionsCondition(object $query, UserInterface $user);

    /**
     * used by UserManager::hasPermission().
     */
    public function hasPermission(?UserInterface $user, TicketInterface $ticket): void;
}
