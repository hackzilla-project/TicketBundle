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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class RoutingTest extends WebTestCase
{
    /**
     * @dataProvider getRoutes
     */
    public function testRoutes(string $name, string $path, array $methods): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');

        $route = $router->getRouteCollection()->get($name);

        $this->assertNotNull($route);
        $this->assertSame($path, $route->getPath());
        $this->assertEmpty(array_diff($methods, $route->getMethods()));

        $matcher = $router->getMatcher();
        $requestContext = $router->getContext();

        foreach ($methods as $method) {
            $requestContext->setMethod($method);
            $match = $matcher->match($path);

            $this->assertSame($name, $match['_route']);
        }
    }

    public function getRoutes(): iterable
    {
        yield ['hackzilla_ticket', '/ticket/', []];
        yield ['hackzilla_ticket_show', '/ticket/{ticketId}/show', []];
        yield ['hackzilla_ticket_new', '/ticket/new', []];
        yield ['hackzilla_ticket_create', '/ticket/create', ['POST']];
        yield ['hackzilla_ticket_delete', '/ticket/{ticketId}/delete', ['DELETE', 'POST']];
        yield ['hackzilla_ticket_reply', '/ticket/{ticketId}/reply', []];
        yield ['hackzilla_ticket_attachment', '/ticket/attachment/{ticketMessageId}/download', []];
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
