<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Functional;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Hackzilla\Bundle\TicketBundle\HackzillaTicketBundle;
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
 */
class TestKernel extends Kernel
{
    use MicroKernelTrait;

    private $useVichUploaderBundle = false;

    public function __construct()
    {
        $this->useVichUploaderBundle = \class_exists(VichUploaderBundle::class);

        parent::__construct('test'.(int) $this->useVichUploaderBundle, true);
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
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
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        // FrameworkBundle config
        $c->loadFromExtension('framework', [
            'secret'         => 'MySecretKey',
            'default_locale' => 'en',
            'translator'     => [
                'fallbacks' => [
                    'en',
                ],
            ],
        ]);

        // SecurityBundle config
        $mainFirewallConfig = ['anonymous' => null];
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
            ],
        ]);

        // TwigBundle config
        $twigConfig = [
            'strict_variables' => '%kernel.debug%',
            'autoescape'       => 'name',
        ];
        // "default_path" configuration is available since version 3.4.
        if (version_compare(self::VERSION, '3.4', '>=')) {
            $twigConfig['default_path'] = __DIR__.'/Resources/views';
        }
        $c->loadFromExtension('twig', $twigConfig);

        // HackzillaBundle config
        $c->loadFromExtension('hackzilla_ticket', [
            'user_class'         => User::class,
            'translation_domain' => 'HackzillaTicketBundle',
        ]);

        if ($this->useVichUploaderBundle) {
            // FrameworkBundle config
            // "framework.form" is required since "vich_uploader.namer_directory_property"
            // service uses "form.property_accessor" service.
            $c->loadFromExtension('framework', [
                'form' => null,
            ]);

            // VichUploaderBundle config
            $c->loadFromExtension('vich_uploader', [
                'db_driver' => 'orm',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->getBaseDir().'cache';
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return $this->getBaseDir().'log';
    }

    public function serialize()
    {
        return serialize($this->useVichUploaderBundle);
    }

    public function unserialize($str)
    {
        $this->__construct(unserialize($str));
    }

    private function getBaseDir()
    {
        return sys_get_temp_dir().'/hackzilla-ticket-bundle/var/'.(int) $this->useVichUploaderBundle.'/';
    }
}
