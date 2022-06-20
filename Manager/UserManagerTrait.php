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

trait UserManagerTrait
{
    private ?UserManagerInterface $userManager;

    public function setUserManager(UserManagerInterface $userManager): self
    {
        $this->userManager = $userManager;

        return $this;
    }

    protected function getUserManager(): UserManagerInterface
    {
        return $this->userManager;
    }
}
