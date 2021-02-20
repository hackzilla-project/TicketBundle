<?php

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Command;

use Doctrine\Persistence\ObjectRepository;
use Hackzilla\Bundle\TicketBundle\Model\UserInterface;

/**
 * NEXT_MAJOR: Inject the object repository and the user class directly in the command
 * classes and remove this trait.
 *
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
trait UserManagerTrait
{
    /**
     * @var ObjectRepository
     */
    private $userRepository;

    public function __construct(string $name = null, ?ObjectRepository $userRepository = null)
    {
        parent::__construct($name);

        $this->userRepository = $userRepository;

        if (null === $userRepository) {
            @trigger_error(sprintf(
                'Omitting or passing null as argument 2 for "%s()" is deprecated since hackzilla/ticket-bundle 3.x.',
                __METHOD__
            ), E_USER_DEPRECATED);
        }
    }

    private function findUser(string $username): ?UserInterface
    {
        if (null !== $this->userRepository) {
            return $this->userRepository->findBy(['username' => $username]);
        }

        if (!$this->getContainer()->has('fos_user.user_manager')) {
            throw new \RuntimeException(sprintf('Command "%s" requires the service "fos_user.user_manager". Is "friendsofsymfony/user-bundle" installed and enabled?', $this->getName()));
        }

        return $this->getContainer()->get('fos_user.user_manager')->findUserByUsername($username);
    }
}
