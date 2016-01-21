<?php

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Hackzilla\Bundle\TicketBundle\Model\UserInterface;

interface UserManagerInterface
{
    public function getCurrentUser();

    public function getUserById($userId);

    public function hasRole(UserInterface $user, $role);
}
