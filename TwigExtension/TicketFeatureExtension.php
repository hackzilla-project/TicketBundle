<?php

namespace Hackzilla\Bundle\TicketBundle\TwigExtension;

use Hackzilla\Bundle\TicketBundle\Component\TicketFeatures;

class TicketFeatureExtension extends \Twig_Extension
{
    private $ticketFeatures;

    /**
     * @param TicketFeatures $ticketFeatures
     */
    public function __construct(TicketFeatures $ticketFeatures)
    {
        $this->ticketFeatures = $ticketFeatures;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('hasTicketFeature', [$this, 'hasFeature']),
        ];
    }

    /**
     * @param string $feature
     *
     * @return bool|null
     */
    public function hasFeature($feature)
    {
        return $this->ticketFeatures->hasFeature($feature);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ticketFeature';
    }
}
