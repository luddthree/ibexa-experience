<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Persistence;

interface EntriesMapperInterface
{
    /**
     * @return \Ibexa\Contracts\Core\Persistence\ValueObject[]
     */
    public function map(array $rawData): array;
}

class_alias(EntriesMapperInterface::class, 'EzSystems\EzPlatformPageFieldType\Persistence\EntriesMapperInterface');
