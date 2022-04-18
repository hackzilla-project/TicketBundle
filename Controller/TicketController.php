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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Ticket controller.
 */
final class TicketController extends AbstractController
{
    private EventDispatcherInterface $dispatcher;

    private PaginatorInterface $pagination;

    private TicketManager $ticketManager;

    private TranslatorInterface $translator;

    private UserManagerInterface $userManager;

    private array $templates = [];

    public function __construct(
        EventDispatcherInterface $dispatcher,
        PaginatorInterface $pagination,
        ParameterBagInterface $bag,
        TicketManager $ticketManager,
        TranslatorInterface $translator,
        UserManagerInterface $userManager
    ) {
        $this->dispatcher = $dispatcher;
        $this->pagination = $pagination;
        $this->ticketManager = $ticketManager;
        $this->translator = $translator;
        $this->userManager = $userManager;
        $this->templates = $bag->get('hackzilla_ticket.templates');
    }

    /**
     * Lists all Ticket entities.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $userManager = $this->userManager;
        $ticketManager = $this->ticketManager;

        $ticketState = $request->get('state', $this->translator->trans('STATUS_OPEN', [], 'HackzillaTicketBundle'));
        $ticketPriority = $request->get('priority', null);

        $query = $ticketManager->getTicketListQuery(
            $userManager,
            $ticketManager->getTicketStatus($ticketState),
            $ticketManager->getTicketPriority($ticketPriority)
        );

        $pagination = $this->pagination->paginate(
            $query->getQuery(),
            (int) ($request->query->get('page', 1))/*page number*/,
            10/*limit per page*/
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
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $ticketManager = $this->ticketManager;

        $ticket = $ticketManager->createTicket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var TicketMessageInterface $message */
            $message = $ticket->getMessages()->current();
            $message->setStatus(TicketMessageInterface::STATUS_OPEN)
                ->setUser($this->userManager->getCurrentUser());

            $ticketManager->updateTicket($ticket, $message);
            $this->dispatchTicketEvent(TicketEvents::TICKET_CREATE, $ticket);

            return $this->redirect($this->generateUrl('hackzilla_ticket_show', ['ticketId' => $ticket->getId()]));
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
    public function newAction()
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($ticketId)
    {
        $ticketManager = $this->ticketManager;
        $ticket = $ticketManager->getTicketById($ticketId);

        if (!$ticket) {
            return $this->redirect($this->generateUrl('hackzilla_ticket'));
        }

        $currentUser = $this->userManager->getCurrentUser();
        $this->userManager->hasPermission($currentUser, $ticket);

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
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function replyAction(Request $request, $ticketId)
    {
        $ticketManager = $this->ticketManager;
        $ticket = $ticketManager->getTicketById($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException($this->translator->trans('ERROR_FIND_TICKET_ENTITY', [], 'HackzillaTicketBundle'));
        }

        $user = $this->userManager->getCurrentUser();
        $this->userManager->hasPermission($user, $ticket);

        $message = $ticketManager->createMessage($ticket);

        $form = $this->createMessageForm($message);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $message->setUser($user);
            $ticketManager->updateTicket($ticket, $message);
            $this->dispatchTicketEvent(TicketEvents::TICKET_UPDATE, $ticket);

            return $this->redirect($this->generateUrl('hackzilla_ticket_show', ['ticketId' => $ticket->getId()]));
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
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $ticketId)
    {
        $userManager = $this->userManager;
        $user = $userManager->getCurrentUser();

        if (!\is_object($user) || !$userManager->hasRole($user, TicketRole::ADMIN)) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }

        $form = $this->createDeleteForm($ticketId);

        if ($request->isMethod('DELETE')) {
            $form->submit($request->request->get($form->getName()));

            if ($form->isValid()) {
                $ticketManager = $this->ticketManager;
                $ticket = $ticketManager->getTicketById($ticketId);

                if (!$ticket) {
                    throw $this->createNotFoundException($this->translator->trans('ERROR_FIND_TICKET_ENTITY', [], 'HackzillaTicketBundle'));
                }

                $ticketManager->deleteTicket($ticket);
                $this->dispatchTicketEvent(TicketEvents::TICKET_DELETE, $ticket);
            }
        }

        return $this->redirect($this->generateUrl('hackzilla_ticket'));
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
    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder(['id' => $id])
            ->add('id', HiddenType::class)
            ->getForm();
    }

    private function createMessageForm(TicketMessageInterface $message): FormInterface
    {
        $form = $this->createForm(
            TicketMessageType::class,
            $message,
            ['new_ticket' => false]
        );

        return $form;
    }
}
