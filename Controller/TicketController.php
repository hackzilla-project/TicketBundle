<?php

namespace Hackzilla\Bundle\TicketBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hackzilla\Bundle\TicketBundle\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\TicketEvents;
use Hackzilla\Bundle\TicketBundle\Event\TicketEvent;

/**
 * Ticket controller.
 *
 */
class TicketController extends Controller
{
    /**
     * Lists all Ticket entities.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $userManager = $this->get('hackzilla_ticket.user');
        $translator = $this->get('translator');
        
        $ticketState = $request->get('state', $translator->trans('STATUS_OPEN'));
        $ticketPriority = $request->get('priority', null);

        $repositoryTicket = $this->getDoctrine()->getRepository('HackzillaTicketBundle:Ticket');

        $repositoryTicketMessage = $this->getDoctrine()->getRepository('HackzillaTicketBundle:TicketMessage');

        $query = $repositoryTicket->getTicketList($userManager, $repositoryTicketMessage->getTicketStatus($translator, $ticketState), $repositoryTicketMessage->getTicketPriority($translator, $ticketPriority));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('HackzillaTicketBundle:Ticket:index.html.twig', array(
                    'pagination' => $pagination,
                    'ticketState' => $ticketState,
                    'ticketPriority' => $ticketPriority,
        ));
    }

    /**
     * Creates a new Ticket entity.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $userManager = $this->get('hackzilla_ticket.user');
        $ticketManager = $this->get('hackzilla_ticket.ticket_manager');

        $ticket = $ticketManager->createTicket();
        $form = $this->createForm('\Hackzilla\Bundle\TicketBundle\Form\Type\TicketType', $ticket);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $message = $ticket->getMessages()->current();
            $message->setStatus(TicketMessage::STATUS_OPEN)
                    ->setUser($userManager->getCurrentUser())
                    ->setTicket($ticket)
            ;

            $ticketManager->updateTicket($ticket, $message);

            $event = new TicketEvent($ticket);
            $this->get('event_dispatcher')->dispatch(TicketEvents::TICKET_CREATE, $event);

            return $this->redirect($this->generateUrl('hackzilla_ticket_show', array('id' => $ticket->getId())));
        }

        return $this->render('HackzillaTicketBundle:Ticket:new.html.twig', array(
                    'entity' => $ticket,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Ticket entity.
     *
     */
    public function newAction()
    {
        $entity = new Ticket();
        $form = $this->createForm('\Hackzilla\Bundle\TicketBundle\Form\Type\TicketType', $entity);

        return $this->render('HackzillaTicketBundle:Ticket:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Ticket entity.
     *
     * @param Ticket $ticket
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Ticket $ticket=null)
    {
        if(!$ticket){
            return $this->redirect($this->generateUrl('hackzilla_ticket'));

        }
        $userManager = $this->get('hackzilla_ticket.user');
        $this->checkUserPermission($userManager->getCurrentUser(), $ticket);

        $data = array('ticket' => $ticket);

        $message = new TicketMessage();
        $message->setPriority($ticket->getPriority());
        $message->setStatus($ticket->getStatus());

        if (TicketMessage::STATUS_CLOSED != $ticket->getStatus()) {
            $data['form'] = $this->createForm('Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType', $message, [
                'new_ticket' => false,
            ])->createView();
        }

        if ($this->get('hackzilla_ticket.user')->isGranted($userManager->getCurrentUser(), 'ROLE_TICKET_ADMIN')) {
            $data['delete_form'] = $this->createDeleteForm($ticket->getId())->createView();
        }

        return $this->render('HackzillaTicketBundle:Ticket:show.html.twig', $data);
    }

    /**
     * @param \FOS\UserBundle\Model\UserInterface|string $user
     * @param Ticket $ticket
     */
    private function checkUserPermission($user, Ticket $ticket)
    {
        if (!\is_object($user) || (!$this->get('hackzilla_ticket.user')->isGranted($user, 'ROLE_TICKET_ADMIN') && $ticket->getUserCreated() != $user->getId())) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }
    }

    /**
     * Finds and displays a Ticket entity.
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function replyAction(Request $request, Ticket $ticket)
    {
        $userManager = $this->get('hackzilla_ticket.user');
        $ticketManager = $this->get('hackzilla_ticket.ticket_manager');

        $user = $userManager->getCurrentUser();
        $this->checkUserPermission($user, $ticket);

        $message = $ticketManager->createMessage();
        $message->setPriority($ticket->getPriority());

        $form = $this->createForm('\Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType', $message, [
            'new_ticket' => false,
        ]);
        $form->submit($request);

        if ($form->isValid()) {

            $message->setUser($user);
            $message->setTicket($ticket);

            $ticketManager->updateTicket($ticket, $message);
            
            $event = new TicketEvent($ticket);
            $this->get('event_dispatcher')->dispatch(TicketEvents::TICKET_UPDATE, $event);

            return $this->redirect($this->generateUrl('hackzilla_ticket_show', array('id' => $ticket->getId())));
        }

        return $this->showAction($ticket);
    }

    /**
     * Deletes a Ticket entity.
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Ticket $ticket)
    {
        $userManager = $this->get('hackzilla_ticket.user');
        $user = $userManager->getCurrentUser();

        if (!\is_object($user) || !$userManager->isGranted($user, 'ROLE_TICKET_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }

        $form = $this->createDeleteForm($ticket->getId());
        $form->submit($request);

        if ($form->isValid()) {
            if (!$ticket) {
                throw $this->createNotFoundException($this->get('translator')->trans('ERROR_FIND_TICKET_ENTITY'));
            }

            $ticketManager = $this->get('hackzilla_ticket.ticket_manager');
            $ticketManager->deleteTicket($ticket);
            $event = new TicketEvent($ticket);
            $this->get('event_dispatcher')->dispatch(TicketEvents::TICKET_DELETE, $event);

        }

        return $this->redirect($this->generateUrl('hackzilla_ticket'));
    }

    /**
     * Creates a form to delete a Ticket entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
                        ->getForm()
        ;
    }
}
