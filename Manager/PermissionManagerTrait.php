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

trait PermissionManagerTrait
{
    private ?PermissionManagerInterface $permissionManager;

    public function setPermissionManager(PermissionManagerInterface $permissionManager): self
    {
        $this->permissionManager = $permissionManager;

        return $this;
    }

    protected function getPermissionManager(): PermissionManagerInterface
    {
        return $this->permissionManager;
    }
}
