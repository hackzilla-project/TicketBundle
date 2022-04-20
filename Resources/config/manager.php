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

use Doctrine\ORM\EntityRepository;
use Hackzilla\Bundle\TicketBundle\Manager\PermissionManagerInterface;
use Hackzilla\Bundle\TicketBundle\Manager\TicketManager;
use Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface;
use Hackzilla\Bundle\TicketBundle\Manager\UserManager;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('hackzilla_ticket.user_repository', EntityRepository::class)
            ->factory([
                new ReferenceConfigurator('doctrine.orm.entity_manager'),
                'getRepository',
            ])
            ->args([
                '%hackzilla_ticket.model.user.class%',
            ])

        ->set(UserManager::class)
            ->public()
            ->args([
                new ReferenceConfigurator('security.token_storage'),
                new ReferenceConfigurator('hackzilla_ticket.user_repository'),
                new ReferenceConfigurator('security.authorization_checker'),
            ])
        ->call('setPermissionManager', [
            new ReferenceConfigurator(PermissionManagerInterface::class),
        ])

        ->alias(UserManagerInterface::class, UserManager::class)
            ->public()

        ->set(PermissionManagerInterface::class)
            ->class('%hackzilla_ticket.manager.permission.class%')
            ->public()
            ->call('setUserManager', [
                new ReferenceConfigurator(UserManagerInterface::class),
            ])

        ->set(TicketManager::class)
            ->public()
            ->args([
                '%hackzilla_ticket.model.ticket.class%',
                '%hackzilla_ticket.model.message.class%',
            ])
            ->call('setPermissionManager', [
                new ReferenceConfigurator(PermissionManagerInterface::class),
            ])
            ->call('setUserManager', [
                new ReferenceConfigurator(UserManagerInterface::class),
            ])
            ->call('setObjectManager', [
                new ReferenceConfigurator('doctrine.orm.entity_manager'),
            ])
            ->call('setTranslator', [
                new ReferenceConfigurator('translator'),
            ])
            ->call('setLogger', [
                new ReferenceConfigurator('logger'),
            ])

        ->alias(TicketManagerInterface::class, TicketManager::class)
            ->public();
};
