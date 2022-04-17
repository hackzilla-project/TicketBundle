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

interface PermissionsServiceInterface
{
    /**
     * used in TicketManager::getTicketListQuery().
     *
     * @param $query
     *
     * @return void
     */
    public function addUserPermissionsCondition($query, UserInterface $user, UserManagerInterface $userManager);

    /**
     * used by UserManager::hasPermission().
     *
     * @param UserInterface|string $user
     */
    public function hasPermission($user, TicketInterface $ticket, UserManagerInterface $userManager): void;
}
