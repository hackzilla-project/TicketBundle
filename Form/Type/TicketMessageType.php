<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketMessageType extends AbstractType
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
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
                method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Symfony\Component\Form\Extension\Core\Type\TextareaType' : 'textarea',
                array(
                    'label'    => 'LABEL_MESSAGE',
                    'required' => false,
                )
            )
            ->add(
                'priority',
                method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Hackzilla\Bundle\TicketBundle\Form\Type\PriorityType' : new PriorityType(),
                array(
                    'label' => 'LABEL_PRIORITY',
                )
            );

        // if existing ticket add status
        if (isset($options['new_ticket']) && !$options['new_ticket']) {
            $user = $this->userManager->getCurrentUser();

            if ($this->userManager->hasRole($user, TicketRole::ADMIN)) {
                $builder->add(
                    'status',
                    method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Hackzilla\Bundle\TicketBundle\Form\Type\StatusType' : new StatusType(),
                    array(
                        'label' => 'LABEL_STATUS',
                    )
                );
            } else {
                $statusTransformer = new StatusTransformer();

                $builder
                    ->add(
                        $builder->create(
                            'status',
                            method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Symfony\Component\Form\Extension\Core\Type\CheckboxType' : 'checkbox',
                            array(
                                'label'    => 'LABEL_MARK_SOLVED',
                                'required' => false,
                                'value'    => 'STATUS_CLOSED',
                            )
                        )->addModelTransformer($statusTransformer)
                    );
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Hackzilla\Bundle\TicketBundle\Entity\TicketMessage',
                'new_ticket' => false,
            )
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
