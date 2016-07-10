<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketMessageType extends AbstractType
{
    private $userManager;
    private $messageClass;

    public function __construct(UserManagerInterface $userManager, $messageClass)
    {
        $this->userManager = $userManager;
        $this->messageClass = $messageClass;
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
                TextareaType::class,
                [
                    'label'    => 'LABEL_MESSAGE',
                    'required' => false,
                ]
            )
            ->add(
                'priority',
                PriorityType::class,
                [
                    'label' => 'LABEL_PRIORITY',
                ]
            )
        ;

        // if existing ticket add status
        if (isset($options['new_ticket']) && !$options['new_ticket']) {
            $user = $this->userManager->getCurrentUser();

            if ($this->userManager->hasRole($user, TicketRole::ADMIN)) {
                $builder->add(
                    'status',
                    StatusType::class,
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
                            CheckboxType::class,
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
                'data_class' => $this->messageClass,
                'new_ticket' => false,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'message';
    }
}
