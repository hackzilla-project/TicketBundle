<?php

namespace Hackzilla\Bundle\TicketBundle\Model\TicketMessage;

use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Symfony\Component\HttpFoundation\File\File;

interface MessageAttachmentInterface extends TicketMessageInterface
{
    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return $this
     */
    public function setAttachmentFile(File $file = null);

    /**
     * @return File
     */
    public function getAttachmentFile();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setAttachmentName($name);

    /**
     * @return string
     */
    public function getAttachmentName();
}
