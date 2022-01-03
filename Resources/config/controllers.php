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
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('hackzilla_ticket.controller.ticket_attachment_controller', TicketAttachmentController::class)
            ->tag('controller.service_arguments')

        ->set('hackzilla_ticket.controller.ticket_controller', TicketController::class)
            ->tag('controller.service_arguments')

    ;
};
