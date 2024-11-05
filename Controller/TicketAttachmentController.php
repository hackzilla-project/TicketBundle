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

namespace Hackzilla\Bundle\TicketBundle\Controller;

use Hackzilla\Bundle\TicketBundle\Manager\TicketManager;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\Model\MessageAttachmentInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Ticket Attachment controller.
 *
 * Download attachments
 */
final class TicketAttachmentController extends AbstractController
{
    public function __construct(private readonly DownloadHandler $downloadHandler, private readonly TicketManager $ticketManager, private readonly TranslatorInterface $translator, private readonly UserManagerInterface $userManager)
    {
    }

    /**
     * Download attachment on message.
     */
    public function downloadAction(int $ticketMessageId): Response
    {
        $ticketMessage = $this->ticketManager->getMessageById($ticketMessageId);

        if (!$ticketMessage instanceof MessageAttachmentInterface) {
            throw $this->createNotFoundException($this->translator->trans('ERROR_FIND_TICKET_ENTITY', [], 'HackzillaTicketBundle'));
        }

        // check permissions
        if (!$this->userManager->hasPermission($this->userManager->getCurrentUser(), $ticketMessage->getTicket())) {
            throw new AccessDeniedHttpException();
        }

        return $this->downloadHandler->downloadObject($ticketMessage, 'attachmentFile');
    }
}
