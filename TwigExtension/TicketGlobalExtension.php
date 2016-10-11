<?php

namespace Hackzilla\Bundle\TicketBundle\TwigExtension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class TicketGlobalExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return [
            'hackzilla_ticket' => [
                'templates' => [
                    'index'           => $this->container->getParameter('hackzilla_ticket.templates.index'),
                    'new'             => $this->container->getParameter('hackzilla_ticket.templates.new'),
                    'show'            => $this->container->getParameter('hackzilla_ticket.templates.show'),
                    'show_attachment' => $this->container->getParameter('hackzilla_ticket.templates.show_attachment'),
                    'prototype'       => $this->container->getParameter('hackzilla_ticket.templates.prototype'),
                    'macros'          => $this->container->getParameter('hackzilla_ticket.templates.macros'),
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ticketGlobal';
    }
}
