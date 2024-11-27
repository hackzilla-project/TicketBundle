<?php

declare(strict_types=1);

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Model;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait MessageAttachmentTrait
{
    /**
     * NOTE: This field is not persisted to database!
     *
     * @Vich\UploadableField(mapping="ticket_message_attachment", fileNameProperty="attachmentName", originalName="attachmentFile", size="attachmentSize")
     */
    protected ?File $attachmentFile;

    /**
     * {@inheritdoc}
     */
    public function setAttachmentFile(?File $file = null): self
    {
        $this->attachmentFile = $file;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachmentFile(): ?File
    {
        return $this->attachmentFile;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachmentName($name): self
    {
        $this->attachmentName = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachmentName(): ?string
    {
        return $this->attachmentName;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachmentSize($size): self
    {
        $this->attachmentSize = $size;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachmentSize(): ?int
    {
        return $this->attachmentSize;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachmentMimeType($mimeType): self
    {
        $this->attachmentMimeType = $mimeType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachmentMimeType(): ?string
    {
        return $this->attachmentMimeType;
    }
}
