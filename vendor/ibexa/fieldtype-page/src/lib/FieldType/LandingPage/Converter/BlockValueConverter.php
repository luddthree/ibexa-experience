<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\LandingPage\Converter;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;

/**
 * Decodes and encodes page object.
 *
 * @method string encode(\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $object)
 * @method \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue decode(string $json)
 * @method array toArray(\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $object)
 * @method \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue fromArray(array $array)
 */
class BlockValueConverter extends AbstractSerializerBasedConverter
{
    /**
     * @return string
     */
    public function getObjectClass(): string
    {
        return BlockValue::class;
    }
}

class_alias(BlockValueConverter::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Converter\BlockValueConverter');
