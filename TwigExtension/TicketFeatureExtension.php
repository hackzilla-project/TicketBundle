<?php

namespace Hackzilla\Bundle\TicketBundle\TwigExtension;

use Hackzilla\Bundle\TicketBundle\Component\TicketFeatures;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TicketFeatureExtension extends AbstractExtension
{
    private $ticketFeatures;

    public function __construct(TicketFeatures $ticketFeatures)
    {
        $this->ticketFeatures = $ticketFeatures;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('hasTicketFeature', [$this, 'hasFeature']),
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
