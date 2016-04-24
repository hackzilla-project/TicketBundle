<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType;
use Hackzilla\Bundle\TicketBundle\Form\Type\TicketType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class TicketTypeTest extends TypeTestCase
{
    private $user;

    protected function setUp()
    {
        $this->user = $this->getMock('Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface');

        parent::setUp();
    }

    protected function getExtensions()
    {
        $ticketType = new TicketType($this->user);
        $ticketMessageType = new TicketMessageType($this->user);

        return [
            new PreloadedExtension(
                [
                    $ticketType->getBlockPrefix()        => $ticketType,
                    $ticketMessageType->getBlockPrefix() => $ticketMessageType,
                ], []
            ),
        ];
    }

    public function testSubmitValidData()
    {
        $formData = [];

        $data = new \Hackzilla\Bundle\TicketBundle\Entity\Ticket();

        $form = $this->factory->create(
            method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Hackzilla\Bundle\TicketBundle\Form\Type\TicketType' : new TicketType($this->user)
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
