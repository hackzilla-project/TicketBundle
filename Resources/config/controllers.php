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

use Hackzilla\Bundle\TicketBundle\Controller\TicketAttachmentController;
use Hackzilla\Bundle\TicketBundle\Controller\TicketController;
use Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Translation\Translator;

return static function (ContainerConfigurator $container): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $container->services()

        ->set('hackzilla_ticket.controller.ticket_attachment_controller', TicketAttachmentController::class)
            ->args([
                new ReferenceConfigurator('vich_uploader.download_handler'),
                new ReferenceConfigurator(TicketManagerInterface::class),
                new ReferenceConfigurator(Translator::class),
                new ReferenceConfigurator(UserManagerInterface::class),
            ])
            ->tag('controller.service_arguments')

        ->set('hackzilla_ticket.controller.ticket_controller', TicketController::class)
            ->args([
                new ReferenceConfigurator(EventDispatcher::class),
                new ReferenceConfigurator(PaginatorInterface::class),
                new ReferenceConfigurator(ParameterBagInterface::class),
                new ReferenceConfigurator(TicketManagerInterface::class),
                new ReferenceConfigurator(Translator::class),
                new ReferenceConfigurator(UserManagerInterface::class),
            ])
            ->tag('controller.service_arguments')

    ;
};
