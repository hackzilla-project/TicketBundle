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

namespace Hackzilla\Bundle\TicketBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Hackzilla\Bundle\TicketBundle\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @final since hackzilla/ticket-bundle 3.x.
 */
class AutoClosingCommand extends Command
{
    use UserManagerAwareTrait;

    protected static $defaultName = 'ticket:autoclosing';

    /**
     * @var TicketManagerInterface
     */
    private $ticketManager;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $locale = 'en';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TicketManagerInterface $ticketManager, ?UserManagerInterface $userManager = null, EntityManagerInterface $entityManager, TranslatorInterface $translator, ParameterBagInterface $parameterBag)
    {
        if (null === $userManager) {
            throw new \TypeError(sprintf('Argument 2 passed to "%s()" must be an instance of "%s". Is "friendsofsymfony/user-bundle" installed and enabled?', __METHOD__, UserManagerInterface::class));
        }

        parent::__construct();

        $this->ticketManager = $ticketManager;
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        if ($parameterBag->has('locale')) {
            $this->locale = $parameterBag->get('locale');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
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
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ticketManager = $this->getContainer()->get('hackzilla_ticket.ticket_manager');
        $ticketRepository = $this->getContainer()->get('doctrine')->getRepository(Ticket::class);

        $locale = $this->getContainer()->hasParameter('locale') ? $this->getContainer()->getParameter('locale') : 'en';
        $translator = $this->getContainer()->get('translator');
        $translator->setLocale($locale);

        $translationDomain = $this->getContainer()->getParameter('hackzilla_ticket.translation_domain');

        $username = $input->getArgument('username');

        $resolvedTickets = $ticketRepository->getResolvedTicketOlderThan($input->getOption('age'));

        foreach ($resolvedTickets as $ticket) {
            $message = $this->ticketManager->createMessage()
                ->setMessage(
                    $this->translator->trans('MESSAGE_STATUS_CHANGED', ['%status%' => $this->translator->trans('STATUS_CLOSED', [], 'HackzillaTicketBundle')], 'HackzillaTicketBundle')
                )
                ->setStatus(TicketMessage::STATUS_CLOSED)
                ->setPriority($ticket->getPriority())
                ->setUser($this->findUser($username))
                ->setTicket($ticket);

            $ticket->setStatus(TicketMessage::STATUS_CLOSED);

            $this->ticketManager->updateTicket($ticket, $message);

            $output->writeln('The ticket "'.$ticket->getSubject().'" has been closed.');
        }
    }
}
