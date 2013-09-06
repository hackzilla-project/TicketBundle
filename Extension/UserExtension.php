<?php

namespace Hackzilla\Bundle\TicketBundle\Extension;

class UserExtension extends \Twig_Extension
{
    private $userManager;

    public function __construct($container) {
        $this->userManager = $container->get('fos_user.user_manager');
    }

    public function getFilters() {
        return array(
            'isTicketAdmin' => new \Twig_Filter_Method($this, 'isTicketAdmin'),
        );
    }

    public function isTicketAdmin($user, $role)
    {
        if (!is_object($user)) {
            $user = $this->userManager->findUserBy(array(
                'id' => $user,
            ));
        }

        return $user->hasRole($role);
    }

    public function getName()
    {
        return 'hackzilla_ticket_user_extension';
    }
}
