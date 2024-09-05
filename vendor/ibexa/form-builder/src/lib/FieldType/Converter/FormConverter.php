<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Converter;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;

/**
 * Decodes and encodes form object.
 *
 * @method string encode(\Ibexa\Contracts\FormBuilder\FieldType\Model\Form $object)
 * @method \Ibexa\Contracts\FormBuilder\FieldType\Model\Form decode(string $json)
 * @method array toArray(\Ibexa\Contracts\FormBuilder\FieldType\Model\Form $object)
 * @method \Ibexa\Contracts\FormBuilder\FieldType\Model\Form fromArray(array $array)
 */
class FormConverter extends AbstractSerializerBasedConverter
{
    /**
     * @return string
     */
    public function getObjectClass(): string
    {
        return Form::class;
    }
}

class_alias(FormConverter::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Converter\FormConverter');
