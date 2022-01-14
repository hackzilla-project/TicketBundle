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
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageWithAttachment;
use Hackzilla\Bundle\TicketBundle\Tests\Functional\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Tests\Functional\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Tests\Functional\Entity\User;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Vich\UploaderBundle\VichUploaderBundle;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 * @author Daniel Platt <github@ofdan.co.uk>
 */
final class TestKernel extends Kernel
{
    use MicroKernelTrait;

    private $useVichUploaderBundle = false;

    public function __construct()
    {
        $this->useVichUploaderBundle = class_exists(VichUploaderBundle::class);

        parent::__construct('test'.(int) $this->useVichUploaderBundle, true);
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

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->import(__DIR__.'/routes.yaml', '/', 'yaml');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        // FrameworkBundle config
        $c->loadFromExtension('framework', [
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
        ]);

        // SecurityBundle config
        $mainFirewallConfig = [];

        // "logout_on_user_change" configuration was marked as mandatory since version 3.4 and deprecated as of 4.1.
        if (version_compare(self::VERSION, '3.4', '>=') && version_compare(self::VERSION, '4.1', '<')) {
            $mainFirewallConfig['logout_on_user_change'] = true;
        }
        $c->loadFromExtension('security', [
            'providers' => [
                'in_memory' => [
                    'memory' => null,
                ],
            ],
            'firewalls' => [
                'main' => $mainFirewallConfig,
            ],
        ]);

        // DoctrineBundle config
        $c->loadFromExtension('doctrine', [
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
            ],
        ]);

        // TwigBundle config
        $twigConfig = [
            'strict_variables' => '%kernel.debug%',
            'exception_controller' => null,
            'autoescape' => 'name',
        ];
        // "default_path" configuration is available since version 3.4.
        if (version_compare(self::VERSION, '3.4', '>=')) {
            $twigConfig['default_path'] = __DIR__.'/Resources/views';
        }
        $c->loadFromExtension('twig', $twigConfig);

        // HackzillaBundle config
        $bundleConfig = [
            'user_class' => User::class,
            'ticket_class' => Ticket::class,
            'message_class' => TicketMessage::class,
        ];

        if ($this->useVichUploaderBundle) {
            $bundleConfig['message_class'] = TicketMessageWithAttachment::class;
        }

        $c->loadFromExtension('hackzilla_ticket', $bundleConfig);

        if ($this->useVichUploaderBundle) {
            // VichUploaderBundle config
            $c->loadFromExtension('vich_uploader', [
                'db_driver' => 'orm',
            ]);
        }
    }
}
