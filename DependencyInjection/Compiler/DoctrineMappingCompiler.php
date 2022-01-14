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

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Hackzilla\Bundle\TicketBundle\DependencyInjection\HackzillaTicketExtension;
use Hackzilla\Bundle\TicketBundle\Model\MessageAttachmentInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DoctrineMappingCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $messageClass = $container->getParameter('hackzilla_ticket.model.message.class');
        $useAttachment = is_subclass_of($messageClass, MessageAttachmentInterface::class);
        $bundleDirectory = HackzillaTicketExtension::bundleDirectory();

        $modelDir = $bundleDirectory.'/Resources/config/doctrine/model/';
        $modelDir .= $useAttachment ? 'attachment' : 'plain';

        $mappings = [
            $modelDir => Model::class,
        ];

        if (class_exists(DoctrineOrmMappingsPass::class)) {
            $container->addCompilerPass(
                DoctrineOrmMappingsPass::createXmlMappingDriver(
                    $mappings
                )
            );
        }
    }
}
