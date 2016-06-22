<?php

namespace Hackzilla\Bundle\TicketBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HackzillaTicketBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $this->buildOrmCompilerPass($container);
    }

    /**
     * Creates and registers compiler passes for ORM mappings.
     *
     * @param ContainerBuilder $container
     */
    private function buildOrmCompilerPass(ContainerBuilder $container)
    {
        if (!class_exists('Doctrine\ORM\Version')) {
            return;
        }

        $entities = [];

//        if (
//            $container->hasParameter('hackzilla_ticket.model.ticket.class')
//            &&
//            $container->getParameter('hackzilla_ticket.model.ticket.class') === 'Hackzilla\Bundle\TicketBundle\Entity\Ticket'
//        ) {
            $entities[realpath(__DIR__.'/Resources/config/doctrine/model/Ticket')] = 'Hackzilla\Bundle\TicketBundle\Entity';
//        }

//        if ($container->getParameter('hackzilla_ticket.model.message.class') === 'Hackzilla\Bundle\TicketBundle\Entity\TicketMessage') {
            $entities[realpath(__DIR__.'/Resources/config/doctrine/model/TicketMessage')] = 'Hackzilla\Bundle\TicketBundle\Entity';
//        }

        if ($entities) {
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createXmlMappingDriver($entities)
            );
        }
    }
}
