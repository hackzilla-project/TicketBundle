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

use Hackzilla\Bundle\TicketBundle\Component\TicketFeatures;
use Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Entity\TicketMessage;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class TicketMessageTypeTest extends TypeTestCase
{
    private MockObject $user;

    protected function setUp(): void
    {
        $this->user = $this->createMock(UserManagerInterface::class);

        parent::setUp();
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'priority' => TicketMessageInterface::PRIORITY_HIGH,
            'message' => null,
        ];

        $data = new TicketMessage();
        $data->setPriority(TicketMessageInterface::PRIORITY_HIGH);

        $form = $this->factory->create(
            TicketMessageType::class,
            null,
            [
                'new_ticket' => true,
            ],
        );

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());

        $formEntity = $form->getData();
        $formEntity->setCreatedAt($data->getCreatedAt());
        $this->assertEquals($data, $formEntity);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    protected function getExtensions(): array
    {
        $ticketMessageType = new TicketMessageType($this->user, new TicketFeatures([], ''), TicketMessage::class);

        return [
            new PreloadedExtension(
                [
                    $ticketMessageType->getBlockPrefix() => $ticketMessageType,
                ],
                []
            ),
        ];
    }
}
