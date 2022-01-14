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

namespace Hackzilla\Bundle\TicketBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass as BaseDoctrineOrmMappingsPass;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\Persistence\Mapping\Driver\SymfonyFileLocator;
use Hackzilla\Bundle\TicketBundle\DependencyInjection\HackzillaTicketExtension;
use Hackzilla\Bundle\TicketBundle\Model\MessageAttachmentInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class DoctrineOrmMappingsPass extends BaseDoctrineOrmMappingsPass
{
    public function __construct($driver = null, array $namespaces = [], $managerParameters = [], $enabledParameter = false, array $aliasMap = [])
    {
        parent::__construct($driver, $namespaces, $managerParameters, $enabledParameter, $aliasMap);
    }

    public function process(ContainerBuilder $container)
    {
        $bundleDirectory = HackzillaTicketExtension::bundleDirectory();
        $namespaces = [];

        if (is_subclass_of($container->getParameter('hackzilla_ticket.model.message.class'), MessageAttachmentInterface::class)) {
            $namespaces[realpath($bundleDirectory.'/Resources/config/doctrine/model/attachment')] = 'Hackzilla\Bundle\TicketBundle\Model';
        } else {
            $namespaces[realpath($bundleDirectory.'/Resources/config/doctrine/model/plain')] = 'Hackzilla\Bundle\TicketBundle\Model';
        }

        $arguments = [$namespaces, '.orm.xml'];
        $locator = new Definition(SymfonyFileLocator::class, $arguments);
        $this->driver = new Definition(XmlDriver::class, [$locator]);
        $this->namespaces = $namespaces;

        parent::process($container);
    }
}
