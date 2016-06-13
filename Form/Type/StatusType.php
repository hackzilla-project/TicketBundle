<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = TicketMessageInterface::STATUSES;
        unset($choices[0]);

        $resolver->setDefaults(
            [
                'choices_as_values' => true,
                'choices'           => array_flip($choices),
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
        return 'status';
    }
}
