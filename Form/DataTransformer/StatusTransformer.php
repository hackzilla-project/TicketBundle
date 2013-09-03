<?php

namespace Hackzilla\Bundle\TicketBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class StatusTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  $number
     * @return string
     */
    public function transform($number)
    {
        echo 'transfrm:';
        var_dump($number);
        if ($number == TicketMessage::STATUS_CLOSED) {
            return 1;
        }

        return null;
    }

    /**
     * Return number.
     *
     * @param  string $number
     *
     * @return $number
     *
     */
    public function reverseTransform($number)
    {
        echo 'reverseTransform:';
        var_dump($number);
        if ($number == 1) {
            return TicketMessage::STATUS_CLOSED;
        }

        return null;
    }
}
