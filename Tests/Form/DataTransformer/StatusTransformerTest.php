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

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\DataTransformer;

use Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer;
use Hackzilla\Bundle\TicketBundle\Model\TicketInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatusTransformerTest extends WebTestCase
{
    private $object;

    protected function setUp(): void
    {
        $mock = $this->getMockBuilder(TicketInterface::class)
            ->setMockClassName('Ticket')
            ->getMock();
        $this->object = new StatusTransformer($mock);
    }

    protected function tearDown(): void
    {
        $this->object = null;
    }

    public function testObjectCreated(): void
    {
        $this->assertInstanceOf(StatusTransformer::class, $this->object);
    }

    public function testTransform(): void
    {
        $this->assertSame(true, $this->object->transform(TicketMessageInterface::STATUS_CLOSED));

        $this->assertNull($this->object->transform('TEST'));
    }

    public function testReverseTransform(): void
    {
        $this->assertSame(TicketMessageInterface::STATUS_CLOSED, $this->object->reverseTransform(1));

        $this->assertNull($this->object->reverseTransform(''));
    }
}
