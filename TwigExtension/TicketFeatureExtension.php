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

namespace Hackzilla\Bundle\TicketBundle\TwigExtension;

use Hackzilla\Bundle\TicketBundle\Component\TicketFeatures;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TicketFeatureExtension extends AbstractExtension
{
    public function __construct(private readonly TicketFeatures $ticketFeatures)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hasTicketFeature', $this->hasFeature(...)),
        ];
    }

    public function hasFeature(string $feature): ?bool
    {
        return $this->ticketFeatures->hasFeature($feature);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'ticketFeature';
    }
}
