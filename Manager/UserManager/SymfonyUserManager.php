<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\UserManager;

use Hackzilla\TicketMessage\Manager\StorageManagerInterface;
use Hackzilla\TicketMessage\Manager\UserManagerInterface;
use Hackzilla\TicketMessage\Model\TicketInterface;
use Hackzilla\TicketMessage\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SymfonyUserManager implements UserManagerInterface
{
    /**
     * @var StorageManagerInterface
     */
    private $storageManger;

    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var string
     */
    private $userRole;

    /**
     * @param StorageManagerInterface       $storageManager
     * @param TokenStorage                  $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string                        $userRole
     */
    public function __construct(
        TokenStorage $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        $userRole
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;

        if (!is_string($userRole) && substr($userRole, 0, 5) !== 'ROLE_') {
            throw new \InvalidArgumentException('User role needs to start with ROLE_');
        }

        $this->userRole = $userRole;
    }

    /**
     * @param StorageManagerInterface $storageManager
     *
     * @return $this
     */
    public function setStorageManager(StorageManagerInterface $storageManager)
    {
        $this->storageManger = $storageManager;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUser($username)
    {
        if (!$this->storageManger) {
            return null;
        }

        $this->storageManger->getUser($username);
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
     * Current user has permission.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function hasRole(UserInterface $user)
    {
        return $this->authorizationChecker->isGranted($this->userRole);
    }

    /**
     * @param UserInterface|string $user
     * @param TicketInterface      $ticket
     */
    public function hasPermission($user, TicketInterface $ticket)
    {
        if (!\is_object($user) || (!$this->hasRole(
                    $user,
                    $this->userRole
                ) && $ticket->getUserCreated() != $user->getId())
        ) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }
    }
}
