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
use Vich\UploaderBundle\Handler\DownloadHandler;
use Hackzilla\Bundle\TicketBundle\Controller\TicketAttachmentController;
use Hackzilla\Bundle\TicketBundle\Controller\TicketController;
use Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $container): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1

    if (class_exists(DownloadHandler::class)) {
        $container->services()
            ->set(TicketAttachmentController::class)
                ->args([
                    new ReferenceConfigurator('vich_uploader.download_handler'),
                    new ReferenceConfigurator(TicketManagerInterface::class),
                    new ReferenceConfigurator('translator'),
                    new ReferenceConfigurator(UserManagerInterface::class),
                ])
                ->call('setContainer', [new ReferenceConfigurator('service_container')])
                ->tag('controller.service_arguments')
                ->alias('hackzilla_ticket.controller.ticket_attachment_controller', TicketAttachmentController::class)
        ;
    }

    $container->services()
        ->set(TicketController::class)
            ->args([
                new ReferenceConfigurator('event_dispatcher'),
                new ReferenceConfigurator('knp_paginator'),
                new ReferenceConfigurator('parameter_bag'),
                new ReferenceConfigurator(TicketManagerInterface::class),
                new ReferenceConfigurator('translator'),
                new ReferenceConfigurator(UserManagerInterface::class),
            ])
            ->call('setContainer', [new ReferenceConfigurator('service_container')])
            ->tag('controller.service_arguments')
            ->alias('hackzilla_ticket.controller.ticket_controller', TicketController::class)
    ;
};
