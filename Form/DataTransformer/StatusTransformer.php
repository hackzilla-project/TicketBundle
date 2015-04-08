<?php

namespace Hackzilla\Bundle\TicketBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class StatusTransformer implements DataTransformerInterface
{
    /**
     * Transforms checkbox value into Ticket Message Status Closed
     *
     * @param  integer $number
     *
     * @return integer|null
     */
    public function transform($number)
    {
       if ($number == TicketMessage::STATUS_CLOSED) {
            return 1;
        }

        return null;
    }

    /**
     * Transforms Ticket Message Status Closed into checkbox value checked
     *
     * @param  integer $number
     *
     * @return integer|null
     */
    public function reverseTransform($number)
    {
        if ($number == 1) {
            return TicketMessage::STATUS_CLOSED;
        }

        return null;
    }
}
