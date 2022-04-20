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

namespace Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Manager;

use Hackzilla\Bundle\TicketBundle\Manager\PermissionManagerInterface;
use Hackzilla\Bundle\TicketBundle\Manager\PermissionManagerTrait;

class TicketPermissionManager implements PermissionManagerInterface
{
    use PermissionManagerTrait;
}
