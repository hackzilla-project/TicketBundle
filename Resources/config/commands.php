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

use Hackzilla\Bundle\TicketBundle\Command\AutoClosingCommand;
use Hackzilla\Bundle\TicketBundle\Command\TicketManagerCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('hackzilla_ticket.command.autoclosing', AutoClosingCommand::class)
            ->tag('console.command', [
                'command' => 'ticket:autoclosing',
            ])
            ->args([
                new ReferenceConfigurator('hackzilla_ticket.ticket_manager'),
                new ReferenceConfigurator('hackzilla_ticket.user_manager'),
                new ReferenceConfigurator('doctrine.orm.entity_manager'),
                new ReferenceConfigurator('parameter_bag'),
                new ReferenceConfigurator('translator'),
            ])

        ->set('hackzilla_ticket.command.create', TicketManagerCommand::class)
            ->tag('console.command', [
                'command' => 'ticket:create',
            ])
            ->args([
                new ReferenceConfigurator('hackzilla_ticket.ticket_manager'),
                new ReferenceConfigurator('hackzilla_ticket.user_manager'),
            ]);
};
