<?php

namespace Hackzilla\Bundle\TicketBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Hackzilla\Bundle\TicketBundle\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Form\TicketType;
use Hackzilla\Bundle\TicketBundle\Form\TicketMessageType;

/**
 * Ticket controller.
 *
 */
class TicketController extends Controller
{

    /**
     * Lists all Ticket entities.
     *
     */
    public function indexAction(Request $request)
    {
        $userManager = $this->get('hackzilla_ticket.user');
        $translator = $this->get('translator');

        $ticketState = $request->get('state', $translator->trans('STATUS_OPEN'));

        $repositoryTicket = $this->getDoctrine()
                ->getRepository('HackzillaTicketBundle:Ticket');

        $repositoryTicketMessage = $this->getDoctrine()
                ->getRepository('HackzillaTicketBundle:TicketMessage');

        $query = $repositoryTicket->getTicketList($userManager, $repositoryTicketMessage->getTicketStatus($translator, $ticketState));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query->getQuery(),
            $request->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('HackzillaTicketBundle:Ticket:index.html.twig', array(
                    'pagination' => $pagination,
                    'ticketState' => $ticketState,
        ));
    }

    /**
     * Creates a new Ticket entity.
     *
     */
    public function createAction(Request $request)
    {
        $userManager = $this->get('hackzilla_ticket.user');

        $ticket = new Ticket();
        $form = $this->createForm(new TicketType($userManager), $ticket);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $message = $ticket->getMessages()->current();
            $message->setStatus(TicketMessage::STATUS_OPEN);
            $message->setUser($userManager->getCurrentUser());
            $message->setTicket($ticket);

            $em->persist($ticket);
            $em->persist($message);

            $em->flush();

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
        $userManager = $this->get('hackzilla_ticket.user');
        $form = $this->createForm(new TicketType($userManager), $entity);

        return $this->render('HackzillaTicketBundle:Ticket:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function showAction(Ticket $ticket)
    {
        $userManager = $this->get('hackzilla_ticket.user');
        $this->checkUserPermission($userManager->getCurrentUser(), $ticket);

        $data = array('ticket' => $ticket);

        $message = new TicketMessage();
        $message->setPriority($ticket->getPriority());
        $message->setStatus($ticket->getStatus());

        if (TicketMessage::STATUS_CLOSED != $ticket->getStatus()) {
            $data['form'] = $this->createForm(new TicketMessageType($userManager), $message)->createView();
        }

        $data['delete_form'] = $this->createDeleteForm($ticket->getId())->createView();

        return $this->render('HackzillaTicketBundle:Ticket:show.html.twig', $data);
    }

    private function checkUserPermission($user, $ticket)
    {
        if (!\is_object($user) || (!$this->get('hackzilla_ticket.user')->hasRole($user, 'ROLE_TICKET_ADMIN') && $ticket->getUserCreated() != $user->getId())) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }
    }

    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function replyAction(Request $request, Ticket $ticket)
    {
        $userManager = $this->get('hackzilla_ticket.user');
        $user = $userManager->getCurrentUser();
        $this->checkUserPermission($user, $ticket);

        $message = new TicketMessage();
        $message->setPriority($ticket->getPriority());

        $form = $this->createForm(new TicketMessageType($userManager), $message);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $message->setUser($user);
            $message->setTicket($ticket);

            $em->persist($message);
            $em->flush();

            return $this->redirect($this->generateUrl('hackzilla_ticket_show', array('id' => $ticket->getId())));
        }

        return $this->showAction($ticket);
    }

    /**
     * Deletes a Ticket entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $userManager = $this->get('hackzilla_ticket.user');
        $user = $userManager->getCurrentUser();

        if (!\is_object($user) || !$userManager->hasRole($user, 'ROLE_TICKET_ADMIN')) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }

        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('HackzillaTicketBundle:Ticket')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException($this->get('translator')->trans('ERROR_FIND_TICKET_ENTITY'));
            }

            foreach ($entity->getMessages() as $message) {
                $em->remove($message);
            }

            $em->remove($entity);
            $em->flush();
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
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

}
