<?php

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Manager;

use Doctrine\Persistence\ObjectRepository;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @final since hackzilla/ticket-bundle 3.x.
 */
class UserManager implements UserManagerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var ObjectRepository
     */
    private $userRepository;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ObjectRepository $userRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->tokenStorage = $tokenStorage;

        if (!is_subclass_of($userRepository->getClassName(), UserInterface::class)) {
            throw new \InvalidArgumentException(sprintf(
                'Argument 2 passed to "%s()" MUST be an object repository for a class implementing "%s".',
                __METHOD__,
                UserInterface::class
            ));
        }

        $this->userRepository = $userRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @return int|UserInterface
     */
    public function getCurrentUser()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ('anon.' === $user) {
            $user = 0;
        } elseif (!$user instanceof UserInterface) {
            throw new \LogicException(sprintf(
                'The object representing the authenticated user MUST implement "%s".',
                UserInterface::class
            ));
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

        return $this->userRepository->find($userId);
    }

    /**
     * Current user has permission.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(UserInterface $user, $role)
    {
        return $this->authorizationChecker->isGranted($role);
    }

    /**
     * @param UserInterface|string $user
     */
    public function hasPermission($user, TicketInterface $ticket)
    {
        if (!\is_object($user) || (!$this->hasRole($user, TicketRole::ADMIN) &&
            $ticket->getUserCreated() != $user->getId())
        ) {
            throw new AccessDeniedHttpException();
        }
    }

    public function findUserByUsername(string $username): ?UserInterface
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }
}
