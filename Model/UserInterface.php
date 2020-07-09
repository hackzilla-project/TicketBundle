<?php

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Model;

interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function getId();

    public function getUsername();

    public function getEmail();
}
