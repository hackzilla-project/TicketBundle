<?php

namespace Hackzilla\Bundle\TicketBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Hackzilla\Bundle\TicketBundle\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class UserLoad
{
    protected $container;

    public function __construct($container) // this is @service_container
    {
        $this->container = $container;
    }
    
    public function getSubscribedEvents()
    {
        return array(
            'postLoad',
        );
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Ticket) {
            if (\is_null($entity->getUserCreatedObject())) {
                $entity->setUserCreated($this->getUserById($entity->getUserCreated()));
            }
            if (\is_null($entity->getLastUserObject())) {
                $entity->setLastUser($this->getUserById($entity->getLastUser()));
            }
        } else if ($entity instanceof TicketMessage) {
            if (\is_null($entity->getUserObject())) {
                $entity->setUser($this->getUserById($entity->getUser()));
            }
        }
    }

    /**
     * Get the user object by id
     * 
     * Would like to remove this somehow to remove the dependency on FOS User Bundle
     */
    public function getUserById($userId)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserBy(array(
            'id' => $userId,
        ));
        
       // var_dump($user);
        return $user;
    }
}
