<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class PriorityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = TicketMessage::$priorities;
        unset($choices[0]);

        $resolver->setDefaults(array(
            'choices' => $choices,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }
}
