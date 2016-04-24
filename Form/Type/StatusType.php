<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = TicketMessage::$statuses;
        unset($choices[0]);

        $resolver->setDefaults(
            array(
                'choices_as_values' => true,
                'choices'           => array_flip($choices),
            )
        );
    }

    public function getParent()
    {
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Symfony\Component\Form\Extension\Core\Type\ChoiceType' : 'choice';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'status';
    }
}
