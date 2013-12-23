<?php

namespace Hackzilla\Bundle\TicketBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Hackzilla\Interfaces\User\UserInterface;

class TicketMessageType extends AbstractType
{
    private $_userManager;
    private $_newTicket;

    public function __construct(UserInterface $userManager, $newTicket = false)
    {        
        $this->_userManager = $userManager;
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
                    'label' => 'LABEL_MESSAGE',
                    'required' => false,
                ))
                ->add('priority', new Type\PriorityType(), array(
                    'label' => 'LABEL_PRIORITY',
                ));

        // if existing ticket add status
        if (!$this->_newTicket) {
            $user = $this->_userManager->getCurrentUser();

            if ($this->_userManager->hasRole($user, 'ROLE_TICKET_ADMIN')) {
                $builder->add('status', new Type\StatusType(), array(
                    'label' => 'LABEL_STATUS',
                ));
            } else {
                $statusTransformer = new DataTransformer\StatusTransformer();

                $builder
                    ->add(
                        $builder->create('status', 'checkbox', array(
                            'label' => 'LABEL_MARK_SOLVED',
                            'required' => false,
                            'value' => 'STATUS_CLOSED',
                        ))
                        ->addModelTransformer($statusTransformer)
                    );
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
