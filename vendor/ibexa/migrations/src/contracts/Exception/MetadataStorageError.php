<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Exception;

use Ibexa\Contracts\Migration\MigrationException;
use RuntimeException;

final class MetadataStorageError extends RuntimeException implements MigrationException
{
    public static function notUpToDate(): self
    {
        return new self('Migrations metadata storage is not up to date. ' . self::createFixMessage());
    }

    public static function notInitialized(): self
    {
        return new self('Migrations metadata storage is not initialized. ' . self::createFixMessage());
    }

    private static function createFixMessage(): string
    {
        return 'Make sure that Doctrine\'s database platform version is specified correctly, '
            . 'try running "ibexa:migrations:migrate command" or calling '
            . 'Ibexa\\Contracts\\Migration\\Metadata\\Storage\\MetadataStorage::ensureInitialized.';
    }
}

class_alias(MetadataStorageError::class, 'Ibexa\Platform\Contracts\Migration\Exception\MetadataStorageError');
