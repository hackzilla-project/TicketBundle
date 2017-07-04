<?php

namespace Hackzilla\Bundle\TicketBundle\Model;

use Hackzilla\TicketMessage\Model\TicketMessageInterface;

class NewTicket
{
    /** @var string */
    private $subject;

    /** @var TicketMessageInterface */
    private $message;

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     *
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return TicketMessageInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param TicketMessageInterface $message
     *
     * @return $this
     */
    public function setMessage(TicketMessageInterface $message)
    {
        $this->message = $message;

        return $this;
    }
}
