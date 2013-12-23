<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;

class StatusType extends AbstractType
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = TicketMessage::$statuses;
        unset($choices[0]);

        $resolver->setDefaults(array(
            'choices' => $choices,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'status';
    }

}
