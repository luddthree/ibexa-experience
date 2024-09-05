<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exception;

use RuntimeException;

final class UnsupportedModelAttributeTypeException extends RuntimeException
{
    /**
     * @param array<string> $allowedTypes
     */
    public function __construct(string $attributeType, array $allowedTypes)
    {
        parent::__construct(
            sprintf(
                'Model attibute type: %s is unsupported. Allowed types: %s',
                $attributeType,
                implode(', ', $allowedTypes)
            )
        );
    }
}
