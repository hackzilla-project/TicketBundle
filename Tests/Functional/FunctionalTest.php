<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Functional;

use Vich\UploaderBundle\Event\Events;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
class FunctionalTest extends WebTestCase
{
    public function testConfiguredTicketManager()
    {
        $this->assertTrue(static::$kernel->getContainer()->has('hackzilla_ticket.ticket_manager'));
    }

    /**
     * @group vichuploaderbundle
     */
    public function testConfiguredFileUploadSubscriber()
    {
        $eventDispatcher = static::$kernel->getContainer()->get('event_dispatcher');
        $listeners = $eventDispatcher->getListeners();

        $this->assertArrayHasKey(Events::POST_UPLOAD, $listeners);
    }
}
