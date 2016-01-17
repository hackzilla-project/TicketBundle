<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketMessageType extends AbstractType
{
    private $_userManager;

    public function __construct(UserManagerInterface $userManager)
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
            ->add(
                'message',
                method_exists(AbstractType::class, 'getBlockPrefix') ? TextareaType::class : 'textarea',
                [
                    'label'    => 'LABEL_MESSAGE',
                    'required' => false,
                ]
            )
            ->add(
                'priority',
                method_exists(AbstractType::class, 'getBlockPrefix') ? PriorityType::class : new PriorityType(),
                [
                    'label' => 'LABEL_PRIORITY',
                ]
            );

        // if existing ticket add status
        if (isset($options['new_ticket']) && !$options['new_ticket']) {
            $user = $this->_userManager->getCurrentUser();

            if ($this->_userManager->isGranted($user, 'ROLE_TICKET_ADMIN')) {
                $builder->add(
                    'status',
                    method_exists(AbstractType::class, 'getBlockPrefix') ? StatusType::class : new StatusType(),
                    [
                        'label' => 'LABEL_STATUS',
                    ]
                );
            } else {
                $statusTransformer = new StatusTransformer();

                $builder
                    ->add(
                        $builder->create(
                            'status',
                            method_exists(AbstractType::class, 'getBlockPrefix') ? CheckboxType::class : 'checkbox',
                            [
                                'label'    => 'LABEL_MARK_SOLVED',
                                'required' => false,
                                'value'    => 'STATUS_CLOSED',
                            ]
                        )->addModelTransformer($statusTransformer)
                    );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TicketMessage::class,
                'new_ticket' => false,
            ]
        );
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
