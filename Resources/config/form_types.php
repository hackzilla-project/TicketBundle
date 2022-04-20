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

use Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType;
use Hackzilla\Bundle\TicketBundle\Form\Type\TicketType;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('hackzilla_ticket.form.type.ticket', TicketType::class)
            ->tag('form.type', [
                'alias' => 'hackzilla_ticket',
            ])
            ->args([
                '%hackzilla_ticket.model.ticket.class%',
            ])

        ->set('hackzilla_ticket.form.type.ticket_message', TicketMessageType::class)
            ->tag('form.type', [
                'alias' => 'hackzilla_ticket_message',
            ])
            ->args([
                new ReferenceConfigurator(UserManagerInterface::class),
                new ReferenceConfigurator('hackzilla_ticket.features'),
                '%hackzilla_ticket.model.message.class%',
            ]);
};
