<?php

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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AutoClosingCommand extends Command
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

    /**
     * BC: Replace 5th argument with "Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface" after bumping to "symfony/dependency-injection:^4.1".
     */
    public function __construct(TicketManagerInterface $ticketManager, UserManagerInterface $userManager, EntityManagerInterface $entityManager, TranslatorInterface $translator, ContainerInterface $container)
    {
        parent::__construct();

        $this->ticketManager = $ticketManager;
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        if ($container->hasParameter('locale')) {
            $this->locale = $container->getParameter('locale');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(static::$defaultName) // BC for symfony/console < 3.4.0
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
        $ticketRepository = $this->entityManager->getRepository(Ticket::class);

        $this->translator->setLocale($this->locale);

        $username = $input->getArgument('username');

        $resolved_tickets = $ticketRepository->getResolvedTicketOlderThan($input->getOption('age'));

        foreach ($resolved_tickets as $ticket) {
            $message = $this->ticketManager->createMessage()
                ->setMessage(
                    $this->translator->trans('MESSAGE_STATUS_CHANGED', ['%status%' => $this->translator->trans('STATUS_CLOSED', [], 'HackzillaTicketBundle')], 'HackzillaTicketBundle')
                )
                ->setStatus(TicketMessage::STATUS_CLOSED)
                ->setPriority($ticket->getPriority())
                ->setUser($this->userManager->findUserByUsername($username))
                ->setTicket($ticket);

            $ticket->setStatus(TicketMessage::STATUS_CLOSED);
            $this->ticketManager->updateTicket($ticket, $message);

            $output->writeln('The ticket "'.$ticket->getSubject().'" has been closed.');
        }
    }
}
