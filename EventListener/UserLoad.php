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
        $userManager = $this->container->get('hackzilla_ticket.user');

        if ($entity instanceof Ticket) {
            if (\is_null($entity->getUserCreatedObject())) {
                $entity->setUserCreated($userManager->getUserById($entity->getUserCreated()));
            }
            if (\is_null($entity->getLastUserObject())) {
                $entity->setLastUser($userManager->getUserById($entity->getLastUser()));
            }
        } else if ($entity instanceof TicketMessage) {
            if (\is_null($entity->getUserObject())) {
                $entity->setUser($userManager->getUserById($entity->getUser()));
            }
        }
    }
}
