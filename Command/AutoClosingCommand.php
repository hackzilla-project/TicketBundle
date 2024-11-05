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

use UnitEnum;
use InvalidArgumentException;
use Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AutoClosingCommand extends Command
{
    protected static $defaultName = 'ticket:autoclosing';

    private bool|string|int|float|UnitEnum|array|null $locale = 'en';

    private string $translationDomain = 'HackzillaTicketBundle';

    /**
     * @var TranslatorInterface
     */
    private readonly LocaleAwareInterface $translator;

    public function __construct(private readonly TicketManagerInterface $ticketManager, private readonly UserManagerInterface $userManager, LocaleAwareInterface $translator, ParameterBagInterface $parameterBag)
    {
        parent::__construct();

        if (!$translator instanceof TranslatorInterface) {
            throw new InvalidArgumentException($translator::class.' Must implement TranslatorInterface and LocaleAwareInterface');
        }

        $this->translator = $translator;
        $this->translator->setLocale($this->locale);

        if ($parameterBag->has('locale')) {
            $this->locale = $parameterBag->get('locale');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
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
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');

        $resolvedTickets = $this->ticketManager->getResolvedTicketOlderThan($input->getOption('age'));

        foreach ($resolvedTickets as $ticket) {
            $message = $this->ticketManager->createMessage()
                ->setMessage(
                    $this->translator->trans('MESSAGE_STATUS_CHANGED', ['%status%' => $this->translator->trans('STATUS_CLOSED', [], $this->translationDomain)], $this->translationDomain)
                )
                ->setStatus(TicketMessageInterface::STATUS_CLOSED)
                ->setPriority($ticket->getPriority())
                ->setUser($this->userManager->findUserByUsername($username))
                ->setTicket($ticket)
            ;

            $ticket->setStatus(TicketMessageInterface::STATUS_CLOSED);
            $this->ticketManager->updateTicket($ticket, $message);

            $output->writeln('The ticket "'.$ticket->getSubject().'" has been closed.');
        }

        return Command::SUCCESS;
    }
}
