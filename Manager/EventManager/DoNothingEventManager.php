<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\EventManager;

use Hackzilla\TicketMessage\Manager\EventManagerInterface;

class DoNothingEventManager implements EventManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle($event, ...$params)
    {
        // Do nothing
    }
}
