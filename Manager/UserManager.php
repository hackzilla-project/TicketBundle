<?php

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Doctrine\ORM\EntityRepository;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class UserManager implements UserManagerInterface
{
    private $tokenStorage;
    private $userRepository;

    public function __construct(
        TokenStorage $tokenStorage,
        EntityRepository $userRepository
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
    }

    /**
     * @return int
     */
    public function getCurrentUser()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user === 'anon.') {
            $user = 0;
        }

        return $user;
    }

    /**
     * @param int $userId
     *
     * @return UserInterface|null
     */
    public function getUserById($userId)
    {
        if (!$userId) {
            return null;
        }

        $user = $this->userRepository->find($userId);

        return $user;
    }

    /**
     * Current user has permission.
     *
     * @param UserInterface $user
     * @param string        $role
     *
     * @return bool
     */
    public function hasRole(UserInterface $user, $role)
    {
        return in_array(strtoupper($role), $user->getRoles(), true);
    }
}
