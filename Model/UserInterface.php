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

namespace Hackzilla\Bundle\TicketBundle\Model;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/* @phpstan-ignore-next-line */
if (\Symfony\Component\HttpKernel\Kernel::MAJOR_VERSION < 5) {
    interface UserInterface extends BaseUserInterface
    {
        public function __toString(): string;

        public function getId();

        public function getEmail(): ?string;
    }
} else {
    interface UserInterface extends PasswordAuthenticatedUserInterface, BaseUserInterface
    {
        public function __toString(): string;

        public function getId();

        public function getEmail(): ?string;
    }
}
