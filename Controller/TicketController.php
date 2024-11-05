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

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Hackzilla\Bundle\TicketBundle\Event\TicketEvent;
use Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType;
use Hackzilla\Bundle\TicketBundle\Form\Type\TicketType;
use Hackzilla\Bundle\TicketBundle\Manager\TicketManager;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\TicketEvents;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Ticket controller.
 */
final class TicketController extends AbstractController
{
    private array $templates = [];

    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly PaginatorInterface $pagination,
        ParameterBagInterface $bag,
        private readonly TicketManager $ticketManager,
        private readonly TranslatorInterface $translator,
        private readonly UserManagerInterface $userManager
    ) {
        $this->templates = $bag->get('hackzilla_ticket.templates');
    }

    /**
     * Lists all Ticket entities.
     */
    public function index(Request $request): Response
    {
        $ticketManager = $this->ticketManager;

        $ticketState = $request->get('state', $this->translator->trans('STATUS_OPEN', [], 'HackzillaTicketBundle'));
        $ticketPriority = $request->get('priority', null);

        $query = $ticketManager->getTicketListQuery(
            $ticketManager->getTicketStatus($ticketState),
            $ticketManager->getTicketPriority($ticketPriority)
        );

        $pagination = $this->pagination->paginate(
            $query->getQuery(),
            (int) ($request->query->get('page', 1))/* page number */,
            10/* limit per page */
        );

        return $this->render(
            $this->templates['index'],
            [
                'pagination' => $pagination,
                'ticketState' => $ticketState,
                'ticketPriority' => $ticketPriority,
                'translationDomain' => 'HackzillaTicketBundle',
            ]
        );
    }

    /**
     * Creates a new Ticket entity.
     */
    public function create(Request $request): RedirectResponse|Response
    {
        $ticketManager = $this->ticketManager;

        $ticket = $ticketManager->createTicket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var TicketMessageInterface $message */
            $message = $ticket->getMessages()->current()
                ->setStatus(TicketMessageInterface::STATUS_OPEN)
                ->setUser($this->userManager->getCurrentUser())
            ;

            $ticketManager->updateTicket($ticket, $message);
            $this->dispatchTicketEvent(TicketEvents::TICKET_CREATE, $ticket);

            return $this->redirectToRoute('hackzilla_ticket_show', ['ticketId' => $ticket->getId()]);
        }

        return $this->render(
            $this->templates['new'],
            [
                'entity' => $ticket,
                'form' => $form->createView(),
                'translationDomain' => 'HackzillaTicketBundle',
            ]
        );
    }

    /**
     * Displays a form to create a new Ticket entity.
     */
    public function new(): Response
    {
        $ticketManager = $this->ticketManager;
        $entity = $ticketManager->createTicket();

        $form = $this->createForm(TicketType::class, $entity);

        return $this->render(
            $this->templates['new'],
            [
                'entity' => $entity,
                'form' => $form->createView(),
                'translationDomain' => 'HackzillaTicketBundle',
            ]
        );
    }

    /**
     * Finds and displays a TicketInterface entity.
     *
     * @param int $ticketId
     */
    public function show($ticketId): RedirectResponse|Response
    {
        $ticketManager = $this->ticketManager;
        $ticket = $ticketManager->getTicketById($ticketId);

        if (!$ticket instanceof TicketInterface) {
            return $this->redirectToRoute('hackzilla_ticket');
        }

        $currentUser = $this->userManager->getCurrentUser();

        if (!$this->userManager->hasPermission($currentUser, $ticket)) {
            throw new AccessDeniedHttpException();
        }

        $data = ['ticket' => $ticket, 'translationDomain' => 'HackzillaTicketBundle'];

        $message = $ticketManager->createMessage($ticket);

        if (TicketMessageInterface::STATUS_CLOSED != $ticket->getStatus()) {
            $data['form'] = $this->createMessageForm($message)->createView();
        }

        if ($currentUser && $this->userManager->hasRole($currentUser, TicketRole::ADMIN)) {
            $data['delete_form'] = $this->createDeleteForm($ticket->getId())->createView();
        }

        return $this->render($this->templates['show'], $data);
    }

    /**
     * Finds and displays a TicketInterface entity.
     *
     * @param int $ticketId
     */
    public function reply(Request $request, $ticketId): RedirectResponse|Response
    {
        $ticketManager = $this->ticketManager;
        $ticket = $ticketManager->getTicketById($ticketId);

        if (!$ticket instanceof TicketInterface) {
            throw $this->createNotFoundException($this->translator->trans('ERROR_FIND_TICKET_ENTITY', [], 'HackzillaTicketBundle'));
        }

        $user = $this->userManager->getCurrentUser();

        if (!$this->userManager->hasPermission($user, $ticket)) {
            throw new AccessDeniedHttpException();
        }

        $message = $ticketManager->createMessage($ticket);

        $form = $this->createMessageForm($message);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $message->setUser($user);
            $ticketManager->updateTicket($ticket, $message);
            $this->dispatchTicketEvent(TicketEvents::TICKET_UPDATE, $ticket);

            return $this->redirectToRoute('hackzilla_ticket_show', ['ticketId' => $ticket->getId()]);
        }

        $data = ['ticket' => $ticket, 'form' => $form->createView(), 'translationDomain' => 'HackzillaTicketBundle'];

        if ($user && $this->userManager->hasRole($user, TicketRole::ADMIN)) {
            $data['delete_form'] = $this->createDeleteForm($ticket->getId())->createView();
        }

        return $this->render($this->templates['show'], $data);
    }

    /**
     * Deletes a Ticket entity.
     *
     * @param int $ticketId
     */
    public function delete(Request $request, $ticketId): RedirectResponse
    {
        $userManager = $this->userManager;
        $user = $userManager->getCurrentUser();

        if (!\is_object($user) || !$userManager->hasRole($user, TicketRole::ADMIN)) {
            throw new HttpException(403);
        }

        $form = $this->createDeleteForm($ticketId);

        if ($request->isMethod('DELETE')) {
            $form->submit($request->request->get($form->getName()));

            if ($form->isValid()) {
                $ticketManager = $this->ticketManager;
                $ticket = $ticketManager->getTicketById($ticketId);

                if (!$ticket instanceof TicketInterface) {
                    throw $this->createNotFoundException($this->translator->trans('ERROR_FIND_TICKET_ENTITY', [], 'HackzillaTicketBundle'));
                }

                $ticketManager->deleteTicket($ticket);
                $this->dispatchTicketEvent(TicketEvents::TICKET_DELETE, $ticket);
            }
        }

        return $this->redirectToRoute('hackzilla_ticket');
    }

    private function dispatchTicketEvent(string $ticketEvent, TicketInterface $ticket): void
    {
        $event = new TicketEvent($ticket);
        $this->dispatcher->dispatch($event, $ticketEvent);
    }

    /**
     * Creates a form to delete a Ticket entity by id.
     *
     * @param mixed $id The entity id
     */
    private function createDeleteForm(mixed $id): FormInterface
    {
        return $this->createFormBuilder(['id' => $id])
            ->add('id', HiddenType::class)
            ->getForm()
        ;
    }

    private function createMessageForm(TicketMessageInterface $message): FormInterface
    {
        return $this->createForm(
            TicketMessageType::class,
            $message,
            [
                'ticket' => $message->getTicket(),
            ]
        );
    }
}
