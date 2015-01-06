<?php

namespace Hackzilla\Bundle\TicketBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class UserExtension extends \Twig_Extension
{
    private $userManager;

    public function __construct(ContainerInterface $container) {
        $this->userManager = $container->get('hackzilla_ticket.user');
    }

    public function getFilters() {
        return array(
            'isTicketAdmin' => new \Twig_Filter_Method($this, 'isTicketAdmin'),
        );
    }

    public function isTicketAdmin($user, $role)
    {
        if (!is_object($user)) {
            $user = $this->userManager->getUserById($user);
        }

        return $user->hasRole($role);
    }

    public function getName()
    {
        return 'hackzilla_ticket_user_extension';
    }
}
