<?php

namespace Hackzilla\Bundle\TicketBundle\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @final since hackzilla/ticket-bundle 3.x.
 */
class TicketGlobalExtension extends AbstractExtension implements GlobalsInterface
{
    protected $templates = [];

    /**
     * @param array $templates
     */
    public function __construct($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return [
            'hackzilla_ticket' => [
                'templates' => [
                    'index'           => $this->templates['index'],
                    'new'             => $this->templates['new'],
                    'show'            => $this->templates['show'],
                    'show_attachment' => $this->templates['show_attachment'],
                    'prototype'       => $this->templates['prototype'],
                    'macros'          => $this->templates['macros'],
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
