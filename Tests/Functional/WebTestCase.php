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

namespace Hackzilla\Bundle\TicketBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

trait CreateKernel
{
    protected static function createKernel(array $options = []): KernelInterface
    {
        return new TestKernel();
    }
}

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
abstract class WebTestCase extends BaseWebTestCase
{
    use CreateKernel;

    protected function setUp(): void
    {
        parent::setUp();

        static::bootKernel();
    }
}
