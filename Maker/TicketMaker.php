<?php

namespace Hackzilla\Bundle\TicketBundle\Maker;

use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageTrait;
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
        $messageRelation = new EntityRelation(EntityRelation::MANY_TO_ONE, $this->getTicketClass(), $this->getMessageClass());
        $messageRelation->setOwningProperty('ticket');
        $messageRelation->setInverseProperty('ticket');

        return [
            ['fieldName' => 'user_created_id', 'type' => 'integer', 'nullable' => false],
            ['fieldName' => 'last_user_id', 'type' => 'integer', 'nullable' => false],
            ['fieldName' => 'last_message', 'type' => 'text', 'nullable' => false],
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
