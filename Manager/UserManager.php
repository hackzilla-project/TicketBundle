<?php

declare(strict_types=1);

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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class UserManager implements UserManagerInterface
{
    use PermissionManagerTrait;

    private ObjectRepository $userRepository;

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        ObjectRepository $userRepository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
        if (!is_subclass_of($userRepository->getClassName(), UserInterface::class)) {
            throw new \InvalidArgumentException(\sprintf('Argument 2 passed to "%s()" MUST be an object repository for a class implementing "%s".', __METHOD__, UserInterface::class));
        }

        $this->userRepository = $userRepository;
    }

    public function getCurrentUser(): ?UserInterface
    {
        if (!$this->tokenStorage->getToken() instanceof TokenInterface) {
            return null;
        }

        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof \Symfony\Component\Security\Core\User\UserInterface && !$user instanceof UserInterface) {
            throw new \LogicException(\sprintf('The object representing the authenticated user MUST implement "%s".', UserInterface::class));
        }

        return $user;
    }

    /**
     * @param int $userId
     */
    public function getUserById($userId): ?UserInterface
    {
        if (!$userId) {
            return null;
        }

        return $this->userRepository->find($userId);
    }

    /**
     * Current user has permission.
     */
    public function hasRole(?UserInterface $user, string $role): bool
    {
        return $this->authorizationChecker->isGranted($role);
    }

    public function hasPermission(?UserInterface $user, TicketInterface $ticket): bool
    {
        try {
            $this->getPermissionManager()->hasPermission($user, $ticket);
        } catch (\Exception) {
            return false;
        }

        return true;
    }

    public function findUserByUsername(string $username): ?UserInterface
    {
        return $this->userRepository->findOneBy(['username' => $username]);
    }
}
