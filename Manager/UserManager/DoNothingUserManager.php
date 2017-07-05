<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\UserManager;

use Hackzilla\TicketMessage\Manager\UserManagerInterface;
use Hackzilla\TicketMessage\Model\TicketInterface;
use Hackzilla\TicketMessage\Model\UserInterface;

class DoNothingUserManager implements UserManagerInterface
{
    /**
     * @inheritDoc
     */
    public function getUser($username)
    {
        return null;
    }

    /**
     * @return int|UserInterface
     */
    public function getCurrentUser()
    {
        return 0;
    }

    /**
     * Current user has permission.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function hasRole(UserInterface $user)
    {
        return false;
    }

    /**
     * @param UserInterface|string $user
     * @param TicketInterface      $ticket
     */
    public function hasPermission($user, TicketInterface $ticket)
    {
        return false;
    }
}
