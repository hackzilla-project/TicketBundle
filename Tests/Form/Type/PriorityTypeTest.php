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

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Hackzilla\Bundle\TicketBundle\Form\Type\PriorityType;
use Symfony\Component\Form\Test\TypeTestCase;

class PriorityTypeTest extends TypeTestCase
{
    private ?PriorityType $object = null;

    protected function setUp(): void
    {
        $this->object = new PriorityType();
    }

    protected function tearDown(): void
    {
        $this->object = null;
    }

    public function testObjectCreated(): void
    {
        $this->assertInstanceOf(PriorityType::class, $this->object);
    }
}
