<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Migration\Exception;

use Ibexa\Contracts\Migration\MigrationException;
use RuntimeException;
use function sprintf;

final class MigrationNotExecuted extends RuntimeException implements MigrationException
{
    public function __construct(string $name)
    {
        parent::__construct(
            sprintf(
                'The provided migration %s has not been executed',
                $name
            ),
        );
    }
}

class_alias(MigrationNotExecuted::class, 'Ibexa\Platform\Contracts\Migration\Exception\MigrationNotExecuted');
