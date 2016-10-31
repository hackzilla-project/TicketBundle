<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\TwigExtension;

use Hackzilla\Bundle\TicketBundle\Component\TicketFeatures;
use Hackzilla\Bundle\TicketBundle\TwigExtension\TicketFeatureExtension;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketFeaturesTest extends WebTestCase
{
    private $object;

    public function setUp()
    {
        $this->object = new TicketFeatureExtension(new TicketFeatures([], ''));
    }

    public function tearDown()
    {
        unset($this->object);
    }

    public function testObjectCreated()
    {
        $this->assertInstanceOf(TicketFeatureExtension::class, $this->object);
    }
}
