<?php

declare(strict_types=1);

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Form\Type;

use Hackzilla\Bundle\TicketBundle\Component\TicketFeatures;
use Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer;
use Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface;
use Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface;
use Hackzilla\Bundle\TicketBundle\TicketRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TicketMessageType extends AbstractType
{
    public function __construct(protected UserManagerInterface $userManager, protected TicketFeatures $features, protected string $messageClass)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'message',
                TextareaType::class,
                [
                    'label' => 'LABEL_MESSAGE',
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

        if ($this->features->hasFeature('attachment')) {
            $builder
                ->add(
                    'attachmentFile',
                    FileType::class,
                    [
                        'label' => 'LABEL_ATTACHMENT',
                        'required' => false,
                    ]
                )
            ;
        }

        // if existing ticket add status
        if (isset($options['ticket']) && $options['ticket']) {
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
                $statusTransformer = new StatusTransformer($options['ticket']);

                $builder
                    ->add(
                        $builder->create(
                            'status',
                            CheckboxType::class,
                            [
                                'label' => 'LABEL_MARK_SOLVED',
                                'required' => false,
                                'value' => TicketMessageInterface::STATUS_CLOSED,
                            ]
                        )->addModelTransformer($statusTransformer)
                    )
                ;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => $this->messageClass,
                'new_ticket' => false,
                'ticket' => null,
                'translation_domain' => 'HackzillaTicketBundle',
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return 'message';
    }
}
