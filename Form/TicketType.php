<?php

namespace Hackzilla\Bundle\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Hackzilla\Interfaces\User\UserInterface;

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
            ->add('subject')
            ->add('messages', 'collection', array(
                'type' => new TicketMessageType($this->_userManager, $newTicket = true),
                'label' => false,
                'allow_add' => true,
//                'by_reference' => false,
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Hackzilla\Bundle\TicketBundle\Entity\Ticket'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'hackzilla_bundle_ticketbundle_tickettype';
    }

}
