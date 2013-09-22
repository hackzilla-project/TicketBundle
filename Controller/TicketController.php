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

        $em = $this->getDoctrine()->getManager();

        $ticketState = $request->get('state', $translator->trans('STATUS_OPEN'));

        $repositoryTicket = $this->getDoctrine()
            ->getRepository('HackzillaTicketBundle:Ticket');
        
        $repositoryTicketMessage = $this->getDoctrine()
            ->getRepository('HackzillaTicketBundle:TicketMessage');
        
        $query = $repositoryTicket->getTicketList($userManager, $repositoryTicketMessage->getTicketStatus($translator, $ticketState));

        $paginator  = $this->get('knp_paginator');
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

        $entity  = new Ticket();
        $form = $this->createForm(new TicketType($userManager), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $userManager->getCurrentUser();
            $message = $entity->getMessages()->current();
            $message->setStatus(TicketMessage::STATUS_OPEN);

            $entity->setUserCreated($user);
            $entity->setLastUser($user);
            $entity->setLastMessage(new \DateTime());
            $entity->setStatus($message->getStatus());
            $entity->setPriority($message->getPriority());

            $message->setTicket($entity);
            $message->setUser($user);

            $em->persist($entity);
            $em->persist($message);

            $em->flush();

            return $this->redirect($this->generateUrl('hackzilla_ticket_show', array('id' => $entity->getId())));
        }

        return $this->render('HackzillaTicketBundle:Ticket:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new Ticket entity.
     *
     */
    public function newAction()
    {
        $entity = new Ticket();
        $securityContext = $this->get('security.context');
        $form   = $this->createForm(new TicketType($securityContext), $entity);

        return $this->render('HackzillaTicketBundle:Ticket:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function showAction(Ticket $ticket)
    {
        $userManager = $this->get('hackzilla_ticket.user');

        if (!$userManager->hasRole('ROLE_TICKET_ADMIN') && $ticket->getUserCreated() != $userManager->getCurrentUser()->getId()) {
             throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }

        $em = $this->getDoctrine()->getManager();

        $data = array();
        $data['ticket'] = $ticket;

        $message = new TicketMessage();
        $message->setPriority($ticket->getPriority());

        if (TicketMessage::STATUS_CLOSED != $ticket->getStatus()) {
            $data['form'] = $this->createForm(new TicketMessageType($userManager), $message)->createView();
        }

        $data['delete_form'] = $this->createDeleteForm($ticket->getId())->createView();

        return $this->render('HackzillaTicketBundle:Ticket:show.html.twig', $data);
    }

    /**
     * Finds and displays a Ticket entity.
     *
     */
    public function replyAction(Request $request, Ticket $ticket)
    {
        $userManager = $this->get('hackzilla_ticket.user');

        if (!$userManager->isGranted('ROLE_TICKET_ADMIN') && $ticket->getUserCreated() != $userManager->getCurrentUser()->getId()) {
             throw new \Symfony\Component\HttpKernel\Exception\HttpException(403);
        }

        $message = new TicketMessage();
        $message->setPriority($ticket->getPriority());
        
        $form = $this->createForm(new TicketMessageType($userManager), $message);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $userManager->getCurrentUser();

            // if ticket not closed, then it'll be set to null
            if (\is_null($message->getStatus())) {
                $message->setStatus($ticket->getStatus());
            } else {
                $ticket->setStatus($message->getStatus());
            }

            $ticket->setUserCreated($user);
            $ticket->setLastUser($user);
            $ticket->setLastMessage(new \DateTime());
            $ticket->setPriority($message->getPriority());
            
            $message->setTicket($ticket);
            $message->setUser($user);

            $em->persist($ticket);
            $em->persist($message);
                        
            $em->flush();

            return $this->redirect($this->generateUrl('hackzilla_ticket_show', array('id' => $ticket->getId())));
        }

        $deleteForm = $this->createDeleteForm($ticket->getId());

        return $this->render('HackzillaTicketBundle:Ticket:show.html.twig', array(
            'ticket' => $ticket,
            'message' => $message,
            'form'   => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Ticket entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $userManager = $this->get('hackzilla_ticket.user');

        if (!$userManager->hasRole('ROLE_TICKET_ADMIN')) {
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
