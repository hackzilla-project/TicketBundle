<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class TicketTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
        );

        $userManager = $this->getMock('Hackzilla\Interfaces\User\UserInterface');
        $this->assertTrue($userManager instanceof \Hackzilla\Interfaces\User\UserInterface);
      
        $type = new \Hackzilla\Bundle\TicketBundle\Form\Type\TicketType($userManager, true);

        $data = new \Hackzilla\Bundle\TicketBundle\Entity\Ticket();
        
        $form = $this->factory->create($type);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($data, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
