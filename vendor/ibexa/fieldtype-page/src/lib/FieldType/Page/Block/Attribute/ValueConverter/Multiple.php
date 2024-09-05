<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\ValueConverterInterface;

class Multiple implements ValueConverterInterface
{
    /**
     * @param string $storageValue
     *
     * @return string[]
     */
    public function fromStorageValue(string $storageValue): array
    {
        return array_map('trim', explode(',', $storageValue));
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[] $value
     *
     * @return string
     */
    public function toStorageValue($value): string
    {
        return implode(',', $value);
    }
}

class_alias(Multiple::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\ValueConverter\Multiple');
