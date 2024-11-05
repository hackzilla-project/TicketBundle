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

interface MessageAttachmentInterface extends TicketMessageInterface
{
    /**
     * @param File|null $file
     *
     * @return $this
     */
    public function setAttachmentFile(?File $file = null): self;

    /**
     * @return File|null
     */
    public function getAttachmentFile(): ?File;

    /**
     * @return $this
     */
    public function setAttachmentName(string $name): self;

    /**
     * @return string|null
     */
    public function getAttachmentName(): ?string;

    /**
     * @param int $size Size in bytes
     *
     * @return $this
     */
    public function setAttachmentSize(int $size): self;

    /**
     * @return int|null
     */
    public function getAttachmentSize(): ?int;

    /**
     * @param string $mimeType Attachment mime type
     *
     * @return $this
     */
    public function setAttachmentMimeType(string $mimeType): self;

    /**
     * @return string|null
     */
    public function getAttachmentMimeType(): ?string;
}
