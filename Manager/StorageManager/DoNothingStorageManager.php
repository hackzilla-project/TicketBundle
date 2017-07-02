<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\StorageManager;

use Hackzilla\TicketMessage\Manager\StorageManagerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class DoNothingStorageManager implements StorageManagerInterface
{
    public function persist($entity)
    {
    }

    public function remove($entity)
    {
    }

    public function flush()
    {
    }

    public function getTicketById($ticketId)
    {
        return null;
    }

    public function getMessageById($ticketMessageId)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function findTicketsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTicketList($ticketStatus, $ticketPriority = null, array $orderBy = null)
    {
        return new Pagerfanta(new ArrayAdapter([]));
    }

    /**
     * {@inheritdoc}
     */
    public function getResolvedTicketOlderThan($days)
    {
        return [];
    }
}
