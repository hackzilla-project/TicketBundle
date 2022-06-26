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

if (is_file($autoloadFile = 'vendor/autoload.php')) {
    $loader = require $autoloadFile;
} elseif (is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    $loader = require $autoloadFile;
} else {
    throw new \LogicException('Run "composer install --dev" to create autoloader.');
}
