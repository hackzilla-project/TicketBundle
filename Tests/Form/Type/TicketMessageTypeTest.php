<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class TicketMessageTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'priority' => TicketMessage::PRIORITY_HIGH,
            'message'  => null,
        );

        $userManager = $this->getMock('Hackzilla\Interfaces\User\UserInterface');
        $this->assertTrue($userManager instanceof \Hackzilla\Interfaces\User\UserInterface);
      
        $type = new \Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType($userManager, true);

        $data = new \Hackzilla\Bundle\TicketBundle\Entity\TicketMessage();
        $data->setPriority(TicketMessage::PRIORITY_HIGH);

        $form = $this->factory->create($type);

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
