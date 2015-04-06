<?php

namespace Hackzilla\Bundle\TicketBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class AutoClosingCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ticket:autoclosing')
            ->setDescription('Automatically close resolved tickets still opened')
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Username of the user who change the status'
            )
            ->addOption(
                'age',
                'a',
                InputOption::VALUE_OPTIONAL,
                'How long since the ticket have been resolved?',
                '10'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ticket_manager = $this->getContainer()->get('hackzilla_ticket.ticket_manager');
        $userManager = $this->getContainer()->get('fos_user.user_manager');
        
        $locale = $this->getContainer()->getParameter('locale') ? $this->getContainer()->getParameter('locale') : 'en';
        $translator = $this->getContainer()->get('translator');
        $translator->setLocale($locale);
        
        $today = new \DateTime();
        $age = $input->getOption('age');
        $username = $input->getArgument('username');
        
        $resolved_tickets = $ticket_manager->findTicketsBy(array(
            'status' => TicketMessage::STATUS_RESOLVED
        ));
        
        foreach ($resolved_tickets as $ticket) {
            $resolve_date = null;
            foreach ($ticket->getMessages() as $ticket_message) {
                if ($ticket_message->getStatus() != TicketMessage::STATUS_RESOLVED) {
                    $resolve_date = null;
                }
                
                // save the moment when the ticket became resolved for the last time
                if ($resolve_date === null && $ticket_message->getStatus() == TicketMessage::STATUS_RESOLVED) {
                    $resolve_date = $ticket_message->getCreatedAt();
                }
            }
            
            if ($today->format('U') - $resolve_date->format('U') > $age * 24 * 3600) {
                $message = $ticket_manager->createMessage();
                $message->setMessage($translator->trans('MESSAGE_STATUS_CHANGED', array('%status%' => $translator->trans('STATUS_CLOSED'))))
                        ->setStatus(TicketMessage::STATUS_CLOSED)
                        ->setPriority($ticket->getPriority())
                        ->setUser($userManager->findUserByUsername($username))
                        ->setTicket($ticket)
                ;
                $ticket->setStatus(TicketMessage::STATUS_CLOSED);
                $ticket_manager->updateTicket($ticket, $message);
                
                $output->writeln('The ticket "'.$ticket->getSubject().'" has been closed.');
            }
        }

    }
}
