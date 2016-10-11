<?php

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Doctrine\ORM\EntityRepository;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserManager implements UserManagerInterface
{
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $userRepository;

    /**
     * @param TokenStorage                  $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param EntityRepository              $userRepository
     */
    public function __construct(
        TokenStorage $tokenStorage,
        EntityRepository $userRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @return int|UserInterface
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
            return;
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
        return $this->authorizationChecker->isGranted($role);
    }

    /**
     * @param \Hackzilla\Bundle\TicketBundle\Model\UserInterface|string $user
     * @param TicketInterface                                           $ticket
     */
    public function hasPermission($user, TicketInterface $ticket)
    {
        if (!\is_object($user) || (!$this->hasRole(
                    $user,
                    TicketRole::ADMIN
                ) && $ticket->getUserCreated() != $user->getId())
        ) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }
    }
}
