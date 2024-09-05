<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Exception;

use Ibexa\Contracts\Migration\MigrationException;
use function sprintf;

final class MigrationAlreadyExecutedException extends \RuntimeException implements MigrationException
{
    public function __construct(string $migrationName)
    {
        parent::__construct(sprintf('"%s" migration is already executed.', $migrationName));
    }
}

class_alias(MigrationAlreadyExecutedException::class, 'Ibexa\Platform\Contracts\Migration\Exception\MigrationAlreadyExecutedException');
