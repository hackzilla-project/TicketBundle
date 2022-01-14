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

/**
 * @method ?UserInterface findUserByUsername(string $username)
 */
interface UserManagerInterface
{
    public function getCurrentUser();

    public function getUserById($userId);

    public function hasRole(UserInterface $user, string $role): bool;

    public function hasPermission(UserInterface|string $user, TicketInterface $ticket);

    public function findUserByUsername(string $username): ?UserInterface;
}
