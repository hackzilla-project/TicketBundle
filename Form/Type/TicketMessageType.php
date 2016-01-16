<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer;
use Hackzilla\Bundle\TicketBundle\User\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketMessageType extends AbstractType
{
    private $_userManager;

    public function __construct(UserInterface $userManager)
    {
        $this->_userManager = $userManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', [
                'label'    => 'LABEL_MESSAGE',
                'required' => false,
            ])
            ->add('priority', 'Hackzilla\Bundle\TicketBundle\Form\Type\PriorityType', [
                'label' => 'LABEL_PRIORITY',
            ]);

        // if existing ticket add status
        if (isset($options['new_ticket']) && !$options['new_ticket']) {
            $user = $this->_userManager->getCurrentUser();

            if ($this->_userManager->isGranted($user, 'ROLE_TICKET_ADMIN')) {
                $builder->add('status', 'Hackzilla\Bundle\TicketBundle\Form\Type\StatusType', [
                    'label' => 'LABEL_STATUS',
                ]);
            } else {
                $statusTransformer = new StatusTransformer();

                $builder
                    ->add(
                        $builder->create('status', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', [
                            'label'    => 'LABEL_MARK_SOLVED',
                            'required' => false,
                            'value'    => 'STATUS_CLOSED',
                        ])->addModelTransformer($statusTransformer)
                    );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Hackzilla\Bundle\TicketBundle\Entity\TicketMessage',
            'new_ticket' => false,
        ]);
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'message';
    }
}
