<?php

namespace Hackzilla\Bundle\TicketBundle\Doctrine;

use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Doctrine\EntityClassGenerator as MakerEntityClassGenerator;

class EntityClassGenerator
{
    private $generator;
    private $doctrineHelper;
    private $managerRegistryClassName = LegacyManagerRegistry::class;

    public function __construct(Generator $generator, DoctrineHelper $doctrineHelper)
    {
        $this->generator = $generator;
        $this->doctrineHelper = $doctrineHelper;

        $this->makerEntityClassGenerator = new MakerEntityClassGenerator($generator, $doctrineHelper);
    }

}
