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

namespace Hackzilla\Bundle\TicketBundle\Tests\Fixtures\Service;

use Hackzilla\Bundle\TicketBundle\Model\PermissionsServiceInterface;
use Hackzilla\Bundle\TicketBundle\Model\PermissionsServiceTrait;

class TicketPermissionService implements PermissionsServiceInterface
{
    use PermissionsServiceTrait;
}
