<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Exception;

use Ibexa\Contracts\Migration\MigrationException;
use Ibexa\Migration\Repository\Migration;
use InvalidArgumentException;
use RuntimeException;

final class InvalidMigrationException extends RuntimeException implements MigrationException
{
    public function __construct(Migration $migration, InvalidArgumentException $e)
    {
        $message = sprintf(
            'Executing migration "%s" failed. Exception: %s.',
            $migration->getName(),
            $e->getMessage(),
        );
        parent::__construct($message, $e->getCode(), $e);
    }
}

class_alias(InvalidMigrationException::class, 'Ibexa\Platform\Contracts\Migration\Exception\InvalidMigrationException');
