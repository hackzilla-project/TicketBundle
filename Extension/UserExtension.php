<?php

namespace Hackzilla\Bundle\TicketBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class UserExtension extends \Twig_Extension
{
    private $userManager;

    public function __construct(ContainerInterface $container)
    {
        $this->userManager = $container->get('hackzilla_ticket.user_manager');
    }

    public function getFilters()
    {
        return [
            'isTicketAdmin' => new \Twig_SimpleFilter('isTicketAdmin', [$this, 'isTicketAdmin']),
        ];
    }

    public function isTicketAdmin($user, $role)
    {
        if (!is_object($user)) {
            $user = $this->userManager->getUserById($user);
        }

        if (is_object($user)) {
            return $this->userManager->hasRole($user, $role);
        } else {
            return false;
        }
    }

    public function getName()
    {
        return 'hackzilla_ticket_user_extension';
    }
}
