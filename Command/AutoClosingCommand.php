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
use Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;

/**
 * @final since hackzilla/ticket-bundle 3.x.
 */
final class AutoClosingCommand extends Command
{
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
     * @var Repository
     */
    private $ticketRepository;

    /**
     * @var string
     */
    private $locale = 'en';

    /**
     * @var string
     */
    private $translationDomain = 'HackzillaTicketBundle';

    /**
     * @var Translator
     */
    private $translator;

    public function __construct(TicketManagerInterface $ticketManager, UserManagerInterface $userManager, EntityManagerInterface $entityManager, LocaleAwareInterface $translator, ParameterBagInterface $parameterBag)
    {
        parent::__construct();

        $this->ticketManager = $ticketManager;
        $this->userManager = $userManager;

        if (!is_a($translator, TranslatorInterface::class)) {
            throw new \InvalidArgumentException(get_class($translator) . " Must implement TranslatorInterface and LocaleAwareInterface");
        }

        $this->translator = $translator;
        $this->translator->setLocale($this->locale);

        if ($parameterBag->has('locale')) {
            $this->locale = $parameterBag->get('locale');
        }

        $ticketClass = $parameterBag->get('hackzilla_ticket.model.ticket.class');
        $this->ticketRepository = $entityManager->getRepository($ticketClass);
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
        $username = $input->getArgument('username');

        $resolvedTickets = $this->ticketRepository->getResolvedTicketOlderThan($input->getOption('age'));

        foreach ($resolvedTickets as $ticket) {
            $message = $this->ticketManager->createMessage()
                ->setMessage(
                    $this->translator->trans('MESSAGE_STATUS_CHANGED', ['%status%' => $this->translator->trans('STATUS_CLOSED', [], $this->translationDomain)], $this->translationDomain)
                )
                ->setStatus(TicketMessageInterface::STATUS_CLOSED)
                ->setPriority($ticket->getPriority())
                ->setUser($this->userManager->findUserByUsername($username))
                ->setTicket($ticket);

            $ticket->setStatus(TicketMessageInterface::STATUS_CLOSED);
            $this->ticketManager->updateTicket($ticket, $message);

            $output->writeln('The ticket "'.$ticket->getSubject().'" has been closed.');
        }

        return Command::SUCCESS;
    }
}
