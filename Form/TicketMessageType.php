<?php

namespace Hackzilla\Bundle\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TicketMessageType extends AbstractType
{
    private $_securityContent;
    private $_newTicket;

    public function __construct(SecurityContextInterface $securityContext, $newTicket = false)
    {        
        $this->_securityContent = $securityContext;
        $this->_newTicket = $newTicket;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('message', 'textarea', array(
                    'required' => false,
                ))
                ->add('priority', new Type\PriorityType());

        // if existing ticket add status
        if (!$this->_newTicket) {
            if ($this->_securityContent->isGranted('ROLE_TICKET_ADMIN')) {
                $builder->add('status', new Type\StatusType());
            } else {
                $statusTransformer = new DataTransformer\StatusTransformer();

                $builder
                    ->add(
                        $builder->create('status', 'checkbox', array(
                            'label' => 'Mark solved',
                            'required' => false,
                            'value' => 'closed',
                        ))
                        ->addModelTransformer($statusTransformer)
                    );
//                $builder->add('status', 'checkbox', array(
//                    'label' => 'Mark solved',
//                    'required' => false,
//                    'value' => 'closed',
//                ));
            }
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Hackzilla\Bundle\TicketBundle\Entity\TicketMessage',
//            'validation_groups' => function(FormInterface $form) {
//                $data = $form->getData();
//                if (Entity\Client::TYPE_PERSON == $data->getType()) {
//                    return array('person');
//                } else {
//                    return array('company');
//                }
//            },
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
