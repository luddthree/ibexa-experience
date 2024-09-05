<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use RuntimeException;

final class UnhandledMatchPropertyException extends RuntimeException
{
    /**
     * @param array<int|string> $properties
     * @param string[] $allowedProperties
     */
    public function __construct(array $properties, array $allowedProperties)
    {
        parent::__construct(
            sprintf(
                'Unhandled Match type: "%s". Only "%s" %s allowed',
                implode('", "', $properties),
                implode('", "', $allowedProperties),
                count($allowedProperties) > 1 ? 'are' : 'is'
            )
        );
    }
}

class_alias(UnhandledMatchPropertyException::class, 'Ibexa\Platform\Bundle\Migration\Bridge\KaliopMigration\Serializer\UnhandledMatchPropertyException');
