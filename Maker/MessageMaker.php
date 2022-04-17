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

use Hackzilla\Bundle\TicketBundle\Model\MessageAttachmentInterface;
use Hackzilla\Bundle\TicketBundle\Model\MessageAttachmentTrait;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageTrait;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Doctrine\EntityRelation;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\MakeEntity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

final class MessageMaker extends AbstractMaker
{
    private $hasAttachment = false;

    public static function getCommandName(): string
    {
        return 'make:entity:message';
    }

    public static function getCommandDescription(): string
    {
        return MakeEntity::getCommandDescription();
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command->addOption('attachment', null, InputOption::VALUE_NONE, 'Overwrite any existing getter/setter methods');

        parent::configureCommand($command, $inputConfig);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $this->hasAttachment = $input->getOption('attachment');

        return parent::generate($input, $io, $generator);
    }

    protected function fields(): array
    {
        $ticketRelation = new EntityRelation(EntityRelation::MANY_TO_ONE, $this->getTicketClass(), $this->getMessageClass());
        $ticketRelation->setOwningProperty('messages');
        $ticketRelation->setInverseProperty('ticket');

        $fields = [
            ['fieldName' => 'user_id', 'type' => 'integer', 'nullable' => false],
            ['fieldName' => 'message', 'type' => 'text', 'nullable' => true],
            ['fieldName' => 'status', 'type' => 'integer', 'nullable' => false],
            ['fieldName' => 'priority', 'type' => 'integer', 'nullable' => false],
            ['fieldName' => 'createdAt', 'type' => 'datetime', 'nullable' => false],
            $ticketRelation,
        ];

        if ($this->hasAttachment()) {
            $fields[] = ['fieldName' => 'attachmentName', 'type' => 'string', 'length' => 255, 'nullable' => true];
            $fields[] = ['fieldName' => 'attachmentSize', 'type' => 'integer', 'nullable' => true];
            $fields[] = ['fieldName' => 'attachmentMimeType', 'type' => 'string', 'length' => 255, 'nullable' => true];
        }

        return $fields;
    }

    protected function entityClass(): string
    {
        return $this->getMessageClass();
    }

    protected function traits(): array
    {
        $traits = [
            TicketMessageTrait::class,
        ];

        if ($this->hasAttachment()) {
            $traits[] = MessageAttachmentTrait::class;
        }

        return $traits;
    }

    protected function interfaces(): array
    {
        $interfaces = [
            TicketMessageInterface::class,
        ];

        if ($this->hasAttachment()) {
            $interfaces[] = MessageAttachmentInterface::class;
        }

        return $interfaces;
    }

    private function hasAttachment(): bool
    {
        return $this->hasAttachment;
    }
}
