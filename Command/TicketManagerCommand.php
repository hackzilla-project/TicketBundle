<?php

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Command;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final since hackzilla/ticket-bundle 3.x.
 */
class TicketManagerCommand extends ContainerAwareCommand
{
    use UserManagerAwareTrait;

    protected static $defaultName = 'ticket:create';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(static::$defaultName) // BC for symfony/console < 3.4.0
            ->setDescription('Create a new Ticket')
            ->addArgument(
                'subject',
                InputArgument::REQUIRED,
                'Enter a subject'
            )
            ->addArgument(
                'message',
                InputArgument::REQUIRED,
                'Enter the message'
            )
            ->addOption(
                'priority',
                'p',
                InputOption::VALUE_OPTIONAL,
                'What priority would it be?',
                '21'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ticketManager = $this->getContainer()->get('hackzilla_ticket.ticket_manager');

        $ticket = $ticketManager->createTicket()
            ->setSubject($input->getArgument('subject'));

        $message = $ticketManager->createMessage()
            ->setMessage($input->getArgument('message'))
            ->setStatus(TicketMessage::STATUS_OPEN)
            ->setPriority($input->getOption('priority'))
            ->setUser($this->findUser('system'));

        $ticketManager->updateTicket($ticket, $message);

        $output->writeln(
            "Ticket with subject '".$ticket->getSubject()."' has been created with ticketnumber #".$ticket->getId().''
        );
    }
}
