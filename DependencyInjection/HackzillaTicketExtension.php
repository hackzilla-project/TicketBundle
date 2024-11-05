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

namespace Hackzilla\Bundle\TicketBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Exception;
use Hackzilla\Bundle\TicketBundle\Manager\PermissionManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
final class HackzillaTicketExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(self::bundleDirectory().'/Resources/config'));
        $loader->load('manager.php');
        $loader->load('maker.php');
        $loader->load('form_types.php');
        $loader->load('event_listener.php');
        $loader->load('component.php');
        $loader->load('controllers.php');
        $loader->load('twig.php');
        $loader->load('commands.php');

        $container->setParameter('hackzilla_ticket.model.user.class', $config['user_class']);
        $container->setParameter('hackzilla_ticket.model.ticket.class', $config['ticket_class']);
        $container->setParameter('hackzilla_ticket.model.message.class', $config['message_class']);

        $permissionClass = $config['permission_class'] ?? PermissionManager::class;

        if (!class_exists($permissionClass)) {
            throw new Exception(sprintf('Permission manager does not exist: %s', $permissionClass));
        }

        $container->setParameter('hackzilla_ticket.manager.permission.class', $permissionClass);

        $container->setParameter('hackzilla_ticket.features', $config['features']);
        $container->setParameter('hackzilla_ticket.templates', $config['templates']);

        $bundles = $container->getParameter('kernel.bundles');
        // Remove "file_upload_subscriber" definition if VichUploaderBundle is not registered
        if (!isset($bundles['VichUploaderBundle'])) {
            $container->removeDefinition('hackzilla_ticket.file_upload_subscriber');
        }
    }

    public static function bundleDirectory(): bool|string
    {
        return realpath(__DIR__.'/..');
    }
}
