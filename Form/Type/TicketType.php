<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
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
                'subject',
                method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Symfony\Component\Form\Extension\Core\Type\TextType' : 'text',
                array(
                    'label' => 'LABEL_SUBJECT',
                )
            )
            ->add(
                'messages',
                method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Symfony\Component\Form\Extension\Core\Type\CollectionType' : 'collection',
                array(
                    method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'entry_type' : 'type'       => method_exists(
                        'Symfony\Component\Form\AbstractType',
                        'getBlockPrefix'
                    ) ? 'Hackzilla\Bundle\TicketBundle\Form\Type\TicketMessageType' : new TicketMessageType($this->userManager),
                    method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'entry_options' : 'options' => array(
                        'new_ticket' => true,
                    ),
                    'label'                                                                            => false,
                    'allow_add'                                                                        => true,
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Hackzilla\Bundle\TicketBundle\Entity\Ticket',
            )
        );
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'ticket';
    }
}
