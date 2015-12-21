<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\User\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    private $_userManager;

    public function __construct(UserInterface $userManager)
    {
        $this->_userManager = $userManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', 'text', array(
                'label' => 'LABEL_SUBJECT',
            ))
            ->add('messages', 'collection', array(
                'type' => 'Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType',
                'options' => [
                    'new_ticket' => true,
                ],
                'label' => false,
                'allow_add' => true,
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Hackzilla\Bundle\TicketBundle\Entity\Ticket',
        ));
    }
}
