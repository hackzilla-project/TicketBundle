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

namespace Hackzilla\Bundle\TicketBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Hackzilla\Bundle\TicketBundle\HackzillaTicketBundle;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\TicketMessageWithAttachment;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\User;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Vich\UploaderBundle\VichUploaderBundle;

trait ConfigureRoutes
{
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__.'/routes.yaml', 'yaml');
    }
}

trait KernelDirectories
{
    public function getCacheDir(): string
    {
        return $this->getBaseDir().'cache';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir(): string
    {
        return $this->getBaseDir().'log';
    }
}

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 * @author Daniel Platt <github@ofdan.co.uk>
 */
final class TestKernel extends Kernel
{
    use ConfigureRoutes, KernelDirectories, MicroKernelTrait {
        ConfigureRoutes::configureRoutes insteadof MicroKernelTrait;
        KernelDirectories::getCacheDir insteadof MicroKernelTrait;
        KernelDirectories::getLogDir insteadof MicroKernelTrait;
    }

    private bool $useVichUploaderBundle;

    public function __construct()
    {
        $this->useVichUploaderBundle = class_exists(VichUploaderBundle::class);

        parent::__construct('test', true);
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new DoctrineBundle(),
            new KnpPaginatorBundle(),
            new TwigBundle(),
            new HackzillaTicketBundle(),
            new TestBundle(),
        ];

        if ($this->useVichUploaderBundle) {
            $bundles[] = new VichUploaderBundle();
        }

        return $bundles;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        // FrameworkBundle config
        $frameworkConfig = [
            'secret' => 'MySecretKey',
            'default_locale' => 'en',
            'session' => [
                'handler_id' => 'session.handler.native_file',
                'name' => 'MOCKSESSID',
            ],
            'translator' => [
                'fallbacks' => [
                    'en',
                ],
            ],
            'form' => null,
            'validation' => [
                'enabled' => true,
            ],
            'test' => true,
        ];

        $container->loadFromExtension('framework', $frameworkConfig);

        // SecurityBundle config
        $mainFirewallConfig = [
            'pattern' => '^/',
            'form_login' => [
                'provider' => 'in_memory',
            ],
        ];
        $securityConfig = [
            'providers' => [
                'in_memory' => [
                    'memory' => null,
                ],
            ],
            'firewalls' => [
                'main' => $mainFirewallConfig,
            ],
        ];

        $container->loadFromExtension('security', $securityConfig);

        // DoctrineBundle config
        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'connections' => [
                    'default' => [
                        'driver' => 'pdo_sqlite',
                    ],
                ],
            ],
            'orm' => [
                'default_entity_manager' => 'default',
                'auto_mapping' => true,
                'mappings' => [
                    'HackzillaTicketBundle' => [
                        'dir' => 'Tests/Fixtures/Entity',
                        'prefix' => 'Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity',
                        'alias' => 'HackzillaTicketBundle',
                        'type' => 'attribute',
                    ],
                ],
            ],
        ]);

        // TwigBundle config
        $twigConfig = [
            'strict_variables' => '%kernel.debug%',
            'exception_controller' => null,
            'autoescape' => 'name',
        ];
        // "default_path" configuration is available since version 3.4.
        $twigConfig['default_path'] = __DIR__.'/Resources/views';
        $container->loadFromExtension('twig', $twigConfig);

        // HackzillaBundle config
        $bundleConfig = [
            'user_class' => User::class,
            'ticket_class' => Ticket::class,
            'message_class' => TicketMessage::class,
        ];

        if ($this->useVichUploaderBundle) {
            $bundleConfig['message_class'] = TicketMessageWithAttachment::class;
        }

        $container->loadFromExtension('hackzilla_ticket', $bundleConfig);

        if ($this->useVichUploaderBundle) {
            // VichUploaderBundle config
            $container->loadFromExtension('vich_uploader', [
                'db_driver' => 'orm',
            ]);
        }
    }

    private function getBaseDir(): string
    {
        return sys_get_temp_dir().'/hackzilla-ticket-bundle/var/'.(int) $this->useVichUploaderBundle.'/';
    }
}
