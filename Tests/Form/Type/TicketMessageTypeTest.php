<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class TicketMessageTypeTest extends TypeTestCase
{
    private $user;

    protected function setUp()
    {
        $this->user = $this->getMock('Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface');

        parent::setUp();
    }

    protected function getExtensions()
    {
        $ticketMessageType = new TicketMessageType($this->user);

        return [
            new PreloadedExtension(
                [
                    $ticketMessageType->getBlockPrefix() => $ticketMessageType,
                ], []
            ),
        ];
    }

    public function testSubmitValidData()
    {
        $formData = [
            'priority' => TicketMessage::PRIORITY_HIGH,
            'message'  => null,
        ];

        $data = new TicketMessage();
        $data->setPriority(TicketMessage::PRIORITY_HIGH);

        $form = $this->factory->create(
            method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType' : new TicketMessageType($this->user),
            null,
            [
                'new_ticket' => true,
            ]
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
}
