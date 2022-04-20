<?php

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
