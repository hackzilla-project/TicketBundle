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

use Hackzilla\Bundle\TicketBundle\TwigExtension\TicketFeatureExtension;
use Hackzilla\Bundle\TicketBundle\TwigExtension\TicketGlobalExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('hackzilla_ticket.component.twig_extension.ticket_features', TicketFeatureExtension::class)
            ->args([
                new ReferenceConfigurator('hackzilla_ticket.features'),
            ])

        ->set('hackzilla_ticket.component.twig_extension.ticket_global', TicketGlobalExtension::class)
            ->args([
                '%hackzilla_ticket.templates%',
            ])
    ;
};
