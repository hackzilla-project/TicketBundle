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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('hackzilla_ticket.maker.ticket', \Hackzilla\Bundle\TicketBundle\Maker\TicketMaker::class)
            ->args([
                new ReferenceConfigurator('maker.file_manager'),
                new ReferenceConfigurator('maker.doctrine_helper'),
                new ReferenceConfigurator('parameter_bag'),
            ])
            ->tag('maker.command')

        ->set('hackzilla_ticket.maker.message', \Hackzilla\Bundle\TicketBundle\Maker\MessageMaker::class)
            ->args([
                new ReferenceConfigurator('maker.file_manager'),
                new ReferenceConfigurator('maker.doctrine_helper'),
                new ReferenceConfigurator('parameter_bag'),
            ])
            ->tag('maker.command');
};
