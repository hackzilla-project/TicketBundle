<?php

namespace Hackzilla\Bundle\TicketBundle\User;

interface UserInterface
{
    public function getCurrentUser();

    public function getUserById($userId);

    public function hasRole($user, $role);

    public function isGranted($user, $role);
}
