<?php

namespace Hackzilla\Bundle\TicketBundle\User;

class DummyUser implements \Hackzilla\Interfaces\User\UserInterface
{
    function __construct($container)
    {
        $this->securityContext = $container->get('security.context');
    }
 
    public function getCurrentUser()
    {
        $user = $this->securityContext->getToken()->getUser();
 
        return $user;
    }
 
    public function getUserById($userId)
    {
        $user = $this->getCurrentUser();

        return $user;
    }
 
    public function hasRole($user, $role)
    {
        return $this->securityContext->isGranted($role);
    }
}
