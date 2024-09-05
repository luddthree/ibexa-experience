<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

use PHPStan\DependencyInjection\NeonAdapter;

$adapter = new NeonAdapter();

$config = [];
if (PHP_VERSION_ID < 80000) {
    $config = $adapter->load(__DIR__ . '/phpstan-baseline-7.4.neon');
} else {
    $config = $adapter->load(__DIR__ . '/phpstan-baseline-8.0.neon');
}

return $config;
