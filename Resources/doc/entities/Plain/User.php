<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Hackzilla\TicketMessage\Model\UserInterface;

class User extends BaseUser implements UserInterface
{
    protected $id;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}
