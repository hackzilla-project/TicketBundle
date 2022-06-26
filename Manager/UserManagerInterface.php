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

interface UserManagerInterface
{
    public function getCurrentUser(): ?UserInterface;

    public function getUserById($userId): ?UserInterface;

    public function hasRole(?UserInterface $user, string $role): bool;

    public function hasPermission(?UserInterface $user, TicketInterface $ticket): bool;

    public function findUserByUsername(string $username): ?UserInterface;
}
