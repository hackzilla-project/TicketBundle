<?php

namespace Hackzilla\Bundle\TicketBundle\Form\DataTransformer;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Component\Form\DataTransformerInterface;

class StatusTransformer implements DataTransformerInterface
{
    /**
     * Transforms checkbox value into Ticket Message Status Closed.
     *
     * @param int $number
     *
     * @return int|null
     */
    public function transform($number)
    {
        if ($number == TicketMessage::STATUS_CLOSED) {
            return 1;
        }

        return;
    }

    /**
     * Transforms Ticket Message Status Closed into checkbox value checked.
     *
     * @param int $number
     *
     * @return int|null
     */
    public function reverseTransform($number)
    {
        if ($number == 1) {
            return TicketMessage::STATUS_CLOSED;
        }

        return;
    }
}
