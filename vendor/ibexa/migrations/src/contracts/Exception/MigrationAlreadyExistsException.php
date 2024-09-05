<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Exception;

use Ibexa\Contracts\Migration\MigrationException;
use Ibexa\Migration\Repository\Migration;
use RuntimeException;
use function sprintf;

final class MigrationAlreadyExistsException extends RuntimeException implements MigrationException
{
    public static function new(Migration $migration): self
    {
        $message = sprintf('Migration "%s" already exists.', $migration->getName());

        return new self($message);
    }

    public static function failedReadFallback(Migration $migration): self
    {
        $message = sprintf(
            'Failed to read file from migration storage. Cannot check existing contents. Migration "%s" already exists.',
            $migration->getName(),
        );

        return new self($message);
    }
}

class_alias(MigrationAlreadyExistsException::class, 'Ibexa\Platform\Contracts\Migration\Exception\MigrationAlreadyExistsException');
