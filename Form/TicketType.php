<?php

namespace Hackzilla\Bundle\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TicketType extends AbstractType
{
    private $_securityContent;

    public function __construct(SecurityContextInterface $securityContext)
    {        
        $this->_securityContent = $securityContext;
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
                'type' => new TicketMessageType($this->_securityContent, $newTicket = true),
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
