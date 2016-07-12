<?php

namespace Hackzilla\Bundle\TicketBundle\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait TicketMessageAttachment
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
     * @var integer $attachmentSize
     */
    protected $attachmentSize;

    /**
     * @var string $attachmentMimeType
     */
    protected $attachmentMimeType;

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


    /**
     * @inheritDoc
     */
    public function setAttachmentSize($size)
    {
        $this->attachmentSize = $size;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttachmentSize()
    {
        return $this->attachmentSize;
    }

    /**
     * @inheritDoc
     */
    public function setAttachmentMimeType($mimeType)
    {
        $this->attachmentMimeType =  $mimeType;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAttachmentMimeType()
    {
        return $this->attachmentMimeType;
    }
}
