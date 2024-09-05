<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter;

interface ValueConverterInterface
{
    /**
     * @param string $storageValue
     *
     * @return mixed
     */
    public function fromStorageValue(string $storageValue);

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function toStorageValue($value): string;
}

class_alias(ValueConverterInterface::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\ValueConverter\ValueConverterInterface');
