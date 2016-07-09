<?php

namespace Hackzilla\Bundle\TicketBundle\Entity;

use Hackzilla\Bundle\TicketBundle\Model\TicketMessage\MessageAttachmentInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Ticket Message.
 *
 * @Vich\Uploadable
 */
class TicketMessageWithAttachment extends TicketMessage implements MessageAttachmentInterface
{
    /**
     * NOTE: This field is not persisted to database!
     *
     * @var File $file
     *
     * @Vich\UploadableField(mapping="ticket_message_attachment", fileNameProperty="attachmentName")
     */
    protected $attachmentFile;

    /**
     * @var string $attachmentName
     */
    protected $attachmentName;

    /**
     * @inheritDoc
     */
    public function setAttachmentFile(File $file = null)
    {
        $this->attachmentFile = $file;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttachmentFile()
    {
        return $this->attachmentFile;
    }

    /**
     * @inheritDoc
     */
    public function setAttachmentName($name)
    {
        $this->attachmentName = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttachmentName()
    {
        return $this->attachmentName;
    }
}
