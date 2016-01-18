<?php

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Doctrine\ORM\EntityRepository;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class UserManager implements UserManagerInterface
{
    private $authorizationChecker;
    private $tokenStorage;
    private $userRepository;

    public function __construct(
        AuthorizationChecker $authorizationChecker,
        TokenStorage $tokenStorage,
        EntityRepository $userRepository
    ) {
        $this->authorizationChecker = $authorizationChecker;
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
