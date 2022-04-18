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

namespace Hackzilla\Bundle\TicketBundle\Maker;

use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketTrait;
use Symfony\Bundle\MakerBundle\Doctrine\EntityRelation;
use Symfony\Bundle\MakerBundle\Maker\MakeEntity;

final class TicketMaker extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:entity:ticket';
    }

    public static function getCommandDescription(): string
    {
        return MakeEntity::getCommandDescription();
    }

    protected function fields(): array
    {
        $lastUserRelation = new EntityRelation(EntityRelation::MANY_TO_ONE, $this->getTicketClass(), $this->getUserClass());
        $lastUserRelation->setOwningProperty('lastUser');
        $lastUserRelation->setInverseProperty('lastUser');
        $lastUserRelation->setMapInverseRelation(false);

        $createdUserRelation = new EntityRelation(EntityRelation::MANY_TO_ONE, $this->getTicketClass(), $this->getUserClass());
        $createdUserRelation->setOwningProperty('createdUser');
        $createdUserRelation->setInverseProperty('createdUser');
        $createdUserRelation->setMapInverseRelation(false);

        $messageRelation = new EntityRelation(EntityRelation::MANY_TO_ONE, $this->getMessageClass(), $this->getTicketClass());
        $messageRelation->setOwningProperty('ticket');
        $messageRelation->setInverseProperty('messages');

        return [
            $createdUserRelation,
            $lastUserRelation,
            ['fieldName' => 'lastMessage', 'type' => 'text', 'nullable' => false],
            ['fieldName' => 'subject', 'type' => 'text', 'nullable' => false],
            ['fieldName' => 'status', 'type' => 'integer', 'nullable' => false],
            ['fieldName' => 'priority', 'type' => 'integer', 'nullable' => false],
            ['fieldName' => 'createdAt', 'type' => 'datetime', 'nullable' => false],
            $messageRelation,
        ];
    }

    protected function entityClass(): string
    {
        return $this->getTicketClass();
    }

    protected function traits(): array
    {
        return [
            TicketTrait::class,
        ];
    }

    protected function interfaces(): array
    {
        return [
            TicketInterface::class,
        ];
    }
}
