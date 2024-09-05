<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Converter;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;

/**
 * Decodes and encodes form object.
 *
 * @method string encode(\Ibexa\Contracts\FormBuilder\FieldType\Model\Field $object)
 * @method \Ibexa\Contracts\FormBuilder\FieldType\Model\Field decode(string $json)
 * @method array toArray(\Ibexa\Contracts\FormBuilder\FieldType\Model\Field $object)
 * @method \Ibexa\Contracts\FormBuilder\FieldType\Model\Field fromArray(array $array)
 */
class FieldConverter extends AbstractSerializerBasedConverter
{
    /**
     * @return string
     */
    public function getObjectClass(): string
    {
        return Field::class;
    }
}

class_alias(FieldConverter::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Converter\FieldConverter');
