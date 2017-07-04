<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Model\NewTicket;
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
                'message',
                TicketMessageType::class,
                [
                    'new_ticket' => true,
                    'label'         => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => NewTicket::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'ticket';
    }
}
