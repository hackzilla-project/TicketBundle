<?php

namespace Hackzilla\Bundle\TicketBundle\Extension;

use Hackzilla\Bundle\TicketBundle\Manager\UserManager;

class UserExtension extends \Twig_Extension
{
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function getFilters()
    {
        return array(
            'isTicketAdmin' => new \Twig_SimpleFilter('isTicketAdmin', array($this, 'isTicketAdmin')),
        );
    }

    public function isTicketAdmin($user, $role)
    {
        if (!is_object($user)) {
            $user = $this->userManager->getUserById($user);
        }

        if (is_object($user)) {
            return $this->userManager->hasRole($user, $role);
        }

        return false;
    }

    public function getName()
    {
        return 'hackzilla_ticket_user_extension';
    }
}
