<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
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
                'choices'           => array_flip($choices),
            ]
        );

        if (substr(\Symfony\Component\HttpKernel\Kernel::VERSION, 0, 2) === '2.') {
            $resolver->setDefaults(
                [
                    'choices_as_values' => true,
                ]
            );
        }
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix()
    {
        return 'status';
    }
}
