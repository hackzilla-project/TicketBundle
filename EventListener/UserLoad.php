<?php

namespace Hackzilla\Bundle\TicketBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Hackzilla\Bundle\TicketBundle\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class UserLoad
{
    protected $userRepository;

    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
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
        
        // Ignore any entity lifecycle events not relating to this bundles entities.
        if(!$entity instanceof Ticket && !$entity instanceof TicketMessage){
            return;
        }
        
        $userRepository = $args->getEntityManager()->getRepository($this->userRepository);

        if ($entity instanceof Ticket) {
            if (\is_null($entity->getUserCreatedObject())) {
                $entity->setUserCreated($userRepository->find($entity->getUserCreated()));
            }
            if (\is_null($entity->getLastUserObject())) {
                $entity->setLastUser($userRepository->find($entity->getLastUser()));
            }
        } elseif ($entity instanceof TicketMessage) {
            if (\is_null($entity->getUserObject())) {
                $entity->setUser($userRepository->find($entity->getUser()));
            }
        }
    }
}
