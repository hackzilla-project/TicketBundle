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

namespace Hackzilla\Bundle\TicketBundle\Tests\Functional\Command;

use Iterator;
use Hackzilla\Bundle\TicketBundle\Command\AutoClosingCommand;
use Hackzilla\Bundle\TicketBundle\Command\TicketManagerCommand;
use Hackzilla\Bundle\TicketBundle\Tests\Functional\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class ApplicationTest extends WebTestCase
{
    /**
     * @dataProvider getCommands
     */
    public function testCommandRegistration(string $expectedClass, string $commandName): void
    {
        $application = new Application(ApplicationTest::$kernel);

        $this->assertInstanceOf($expectedClass, $application->find($commandName));
    }

    public function getCommands(): Iterator
    {
        yield [AutoClosingCommand::class, 'ticket:autoclosing'];
        yield [TicketManagerCommand::class, 'ticket:create'];
    }
}
