<?php

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Entity\Ticket;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
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
                'subject',
                method_exists(AbstractType::class, 'getBlockPrefix') ? TextType::class : 'text',
                [
                    'label' => 'LABEL_SUBJECT',
                ]
            )
            ->add(
                'messages',
                method_exists(AbstractType::class, 'getBlockPrefix') ? CollectionType::class : 'collection',
                [
                    method_exists(AbstractType::class, 'getBlockPrefix') ? 'entry_type' : 'type'       => method_exists(
                        AbstractType::class,
                        'getBlockPrefix'
                    ) ? TicketMessageType::class : new TicketMessageType($this->_userManager),
                    method_exists(AbstractType::class, 'getBlockPrefix') ? 'entry_options' : 'options' => [
                        'new_ticket' => true,
                    ],
                    'label'                                                                            => false,
                    'allow_add'                                                                        => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Ticket::class,
            ]
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
