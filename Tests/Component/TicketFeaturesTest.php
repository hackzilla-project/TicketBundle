<?php

/*
 * This file is part of HackzillaTicketBundle package.
 *
 * (c) Daniel Platt <github@ofdan.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hackzilla\Bundle\TicketBundle\Tests\Extension;

use Hackzilla\Bundle\TicketBundle\Component\TicketFeatures;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessage;
use Hackzilla\Bundle\TicketBundle\Entity\TicketMessageWithAttachment;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TicketFeaturesTest extends WebTestCase
{
    /**
     * @dataProvider constructProvider
     *
     * @param string $class
     */
    public function testConstruct(array $features, $class)
    {
        $this->assertInstanceOf(TicketFeatures::class, new TicketFeatures($features, $class));
    }

    public function constructProvider()
    {
        return [
            [[], \stdClass::class],
        ];
    }

    /**
     * @dataProvider featureAttachmentProvider
     *
     * @param string    $class
     * @param bool|null $compare
     */
    public function testFeatureAttachment(array $features, $class, $compare)
    {
        $obj = new TicketFeatures($features, $class);

        $this->assertInstanceOf(TicketFeatures::class, $obj);
        // NEXT_MAJOR: Remove the argument 2 for `TicketFeatures::hasFeature()`
        $this->assertSame($obj->hasFeature('attachment', 'return_strict_bool'), $compare);
    }

    public function featureAttachmentProvider()
    {
        return [
            [[], TicketMessage::class, false],
            [['attachment' => true], TicketMessage::class, false],
            [['attachment' => true], TicketMessageWithAttachment::class, true],
        ];
    }
}
