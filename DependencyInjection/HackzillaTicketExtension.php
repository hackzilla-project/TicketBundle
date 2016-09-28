<?php

namespace Hackzilla\Bundle\TicketBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HackzillaTicketExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(self::bundleDirectory().'/Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('hackzilla_ticket.model.user.class', $config['user_class']);
        $container->setParameter('hackzilla_ticket.model.ticket.class', $config['ticket_class']);
        $container->setParameter('hackzilla_ticket.model.message.class', $config['message_class']);

        $container->setParameter('hackzilla_ticket.features', $config['features']);
        
        $container->setParameter('hackzilla_ticket.templates.index', $config['templates']['index']);
        $container->setParameter('hackzilla_ticket.templates.new', $config['templates']['new']);
        $container->setParameter('hackzilla_ticket.templates.prototype', $config['templates']['prototype']);
        $container->setParameter('hackzilla_ticket.templates.show', $config['templates']['show']);
        $container->setParameter('hackzilla_ticket.templates.show_attachment', $config['templates']['show_attachment']);
        $container->setParameter('hackzilla_ticket.templates.macros', $config['templates']['macros']);
    }

    public static function bundleDirectory()
    {
        return realpath(__DIR__.'/..');
    }
}
