<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\EventManager;

use Hackzilla\Bundle\TicketBundle\Event\TicketEvent;
use Hackzilla\TicketMessage\Manager\EventManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SymfonyEventManager implements EventManagerInterface
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($eventName, ...$params)
    {
        $event = new TicketEvent($params[0]);
        $this->eventDispatcher->dispatch($eventName, $event);
    }
}
