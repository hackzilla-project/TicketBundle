<?php

namespace Hackzilla\Bundle\TicketBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
class WebTestCase extends BaseWebTestCase
{
    protected function setUp()
    {
        parent::setUp();

        static::bootKernel();
    }

    protected static function createKernel(array $options = [])
    {
        return new TestKernel();
    }
}
