<?php

namespace Hackzilla\Bundle\TicketBundle\Command;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
                'How many days since the ticket was resolved?',
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
        $ticketRepository = $this->getContainer()->get('doctrine')->getRepository('HackzillaTicketBundle:Ticket');

        $locale = $this->getContainer()->getParameter('locale') ? $this->getContainer()->getParameter('locale') : 'en';
        $translator = $this->getContainer()->get('translator');
        $translator->setLocale($locale);

        $username = $input->getArgument('username');

        $resolved_tickets = $ticketRepository->getResolvedTicketOlderThan($input->getOption('age'));

        foreach ($resolved_tickets as $ticket) {
            $message = $ticket_manager->createMessage()
                ->setMessage(
                    $translator->trans('MESSAGE_STATUS_CHANGED', ['%status%' => $translator->trans('STATUS_CLOSED')])
                )
                ->setStatus(TicketMessage::STATUS_CLOSED)
                ->setPriority($ticket->getPriority())
                ->setUser($userManager->findUserByUsername($username))
                ->setTicket($ticket);

            $ticket->setStatus(TicketMessage::STATUS_CLOSED);
            $ticket_manager->updateTicket($ticket, $message);

            $output->writeln('The ticket "'.$ticket->getSubject().'" has been closed.');
        }
    }
}
