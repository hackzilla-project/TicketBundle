<?php

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;

interface UserManagerInterface
{
    public function getCurrentUser();

    public function getUserById($userId);

    public function hasRole(UserInterface $user, $role);

    /**
     * @param \Hackzilla\Bundle\TicketBundle\Model\UserInterface|string $user
     * @param TicketInterface                                           $ticket
     */
    public function hasPermission($user, TicketInterface $ticket);
}
