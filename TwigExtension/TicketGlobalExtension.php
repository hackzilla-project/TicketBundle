<?php

namespace Hackzilla\Bundle\TicketBundle\TwigExtension;

class TicketGlobalExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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
