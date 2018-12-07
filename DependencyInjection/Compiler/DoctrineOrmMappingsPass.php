<?php

namespace Hackzilla\Bundle\TicketBundle\DependencyInjection\Compiler;

use Hackzilla\Bundle\TicketBundle\DependencyInjection\HackzillaTicketExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class DoctrineOrmMappingsPass extends \Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass
{
    public function __construct($driver = null, array $namespaces = [], $managerParameters = [], $enabledParameter = false, array $aliasMap = [])
    {
        parent::__construct($driver, $namespaces, $managerParameters, $enabledParameter, $aliasMap);
    }

    public function process(ContainerBuilder $container)
    {
        $bundleDirectory = HackzillaTicketExtension::bundleDirectory();
        $namespaces      = [];

        if (
            'Hackzilla\Bundle\TicketBundle\Model\TicketWithAttachment' === $container->getParameter('hackzilla_ticket.model.ticket.class')
            ||
            'Hackzilla\Bundle\TicketBundle\Model\TicketMessageWithAttachment' === $container->getParameter('hackzilla_ticket.model.message.class')
        ) {
            $namespaces[realpath($bundleDirectory.'/Resources/config/doctrine/model/attachment')] = 'Hackzilla\Bundle\TicketBundle\Model';
        } elseif (
            'Hackzilla\Bundle\TicketBundle\Model\Ticket' === $container->getParameter('hackzilla_ticket.model.ticket.class')
            ||
            'Hackzilla\Bundle\TicketBundle\Model\TicketMessage' === $container->getParameter('hackzilla_ticket.model.message.class')
        ) {
            $namespaces[realpath($bundleDirectory.'/Resources/config/doctrine/model/plain')] = 'Hackzilla\Bundle\TicketBundle\Model';
        }

        $arguments        = [$namespaces, '.orm.xml'];
        $locator          = new Definition('Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator', $arguments);
        $this->driver     = new Definition('Doctrine\ORM\Mapping\Driver\XmlDriver', [$locator]);
        $this->namespaces = $namespaces;

        parent::process($container);
    }
}
