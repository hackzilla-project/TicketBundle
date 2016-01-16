<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriorityType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = TicketMessage::$priorities;
        unset($choices[0]);

        $resolver->setDefaults(
            [
                'choices' => $choices,
            ]
        );
    }

    public function getParent()
    {
        return method_exists(AbstractType::class, 'getBlockPrefix') ? ChoiceType::class : 'choice';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'priority';
    }
}
