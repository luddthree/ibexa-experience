<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\LandingPage\Converter;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;

/**
 * @method string encode(\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute $object)
 * @method \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute decode(string $json)
 * @method array toArray(\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute $object)
 * @method \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute fromArray(array $array)
 */
class AttributeConverter extends AbstractSerializerBasedConverter
{
    /**
     * @return string
     */
    public function getObjectClass(): string
    {
        return Attribute::class;
    }
}

class_alias(AttributeConverter::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Converter\AttributeConverter');
