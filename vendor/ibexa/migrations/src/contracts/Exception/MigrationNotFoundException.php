<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Exception;

use Ibexa\Contracts\Migration\MigrationException;
use RuntimeException;

final class MigrationNotFoundException extends RuntimeException implements MigrationException
{
    public function __construct(string $name)
    {
        $message = sprintf('Migration "%s" not found.', $name);

        parent::__construct($message);
    }
}

class_alias(MigrationNotFoundException::class, 'Ibexa\Platform\Contracts\Migration\Exception\MigrationNotFoundException');
