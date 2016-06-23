<?php

namespace Hackzilla\Bundle\TicketBundle\DependencyInjection\Compiler;

use Hackzilla\Bundle\TicketBundle\DependencyInjection\HackzillaTicketExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class DoctrineOrmMappingsPass extends \Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass
{
    function __construct($driver = null, array $namespaces = [], $managerParameters = [], $enabledParameter = false, array $aliasMap = [])
    {
        parent::__construct($driver, $namespaces, $managerParameters, $enabledParameter, $aliasMap);
    }

    public function process(ContainerBuilder $container)
    {
        $bundleDirectory = HackzillaTicketExtension::bundleDirectory();
        $namespaces = [];

        if (
            $container->getParameter('hackzilla_ticket.model.ticket.class') === 'Hackzilla\Bundle\TicketBundle\Entity\Ticket'
        ) {
            $path = realpath(__DIR__.'/../../Resources/config/doctrine/model/Ticket');
            $namespaces[$path] = 'Hackzilla\Bundle\TicketBundle\Entity';
        }

        if (
            $container->getParameter('hackzilla_ticket.model.message.class') === 'Hackzilla\Bundle\TicketBundle\Entity\TicketMessage'
        ) {
            $path = realpath(__DIR__.'/../../Resources/config/doctrine/model/TicketMessage');
            $namespaces[$path] = 'Hackzilla\Bundle\TicketBundle\Entity';
        }

        $arguments = array($namespaces, '.orm.xml');
        $locator = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);
        $this->driver = new Definition('Doctrine\ORM\Mapping\Driver\XmlDriver', array($locator));
        $this->namespaces = $namespaces;

        parent::process($container);
    }
}
