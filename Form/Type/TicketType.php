<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    protected $ticketClass;

    public function __construct($ticketClass)
    {
        $this->ticketClass = $ticketClass;
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
                TextType::class,
                [
                    'label' => 'LABEL_SUBJECT',
                ]
            )
            ->add(
                'messages',
                CollectionType::class,
                [
                    'entry_type'    => TicketMessageType::class,
                    'entry_options' => [
                        'new_ticket' => true,
                    ],
                    'label'         => false,
                    'allow_add'     => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => $this->ticketClass,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'ticket';
    }
}
