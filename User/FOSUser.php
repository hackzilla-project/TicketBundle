<?php

namespace Hackzilla\Bundle\TicketBundle\User;

use Hackzilla\Bundle\TicketBundle\User\UserInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class FOSUser implements UserInterface
{
    private $securityContext;
    private $userManager;

    function __construct(SecurityContextInterface $securityContext, UserInterface $userManager)
    {
        $this->securityContext = $securityContext;
        $this->userManager = $userManager;
    }

    /**
     * @return mixed
     */
    public function getCurrentUser()
    {
        $user = $this->securityContext->getToken()->getUser();

        return $user;
    }

    /**
     * @param integer $userId
     * @return mixed
     */
    public function getUserById($userId)
    {
        $user = $this->userManager->findUserBy(array(
            'id' => $userId,
        ));

        return $user;
    }

    /**
     * @param $user
     * @param string $role
     * @return boolean
     */
    public function hasRole($user, $role)
    {
        return $user->hasRole($role);
    }

    /**
     * Current user granted permission
     *
     * @param $user
     * @param string $role
     * @return boolean
     */
    public function isGranted($user, $role)
    {
        return $this->securityContext->isGranted($role);
    }
}
