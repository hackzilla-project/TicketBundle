<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Functional\Command;

use Hackzilla\Bundle\TicketBundle\Tests\Functional\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
class ApplicationTest extends WebTestCase
{
    /**
     * @dataProvider getCommands
     *
     * @param string $commandName
     */
    public function testCommandRegistration($commandName)
    {
        $this->markTestSkipped('"friendsofsymfony/user-bundle" must be installed in order to run this test');

        $application = new Application(static::$kernel);

        $this->assertInstanceOf(Command::class, $application->find($commandName));
    }

    public function getCommands()
    {
        return [
            ['ticket:autoclosing'],
            ['ticket:create'],
        ];
    }
}
