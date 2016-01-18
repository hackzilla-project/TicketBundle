<?php

namespace Hackzilla\Bundle\TicketBundle\Model;

interface UserInterface extends \Symfony\Component\Security\Core\User\UserInterface
{
    public function getId();

    public function getUsername();

    public function getEmail();
}
