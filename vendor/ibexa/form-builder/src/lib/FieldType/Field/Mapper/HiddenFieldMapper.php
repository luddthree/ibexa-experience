<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;

class HiddenFieldMapper extends GenericFieldMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapFormOptions(Field $field, array $constraints): array
    {
        $options = parent::mapFormOptions($field, $constraints);
        $options['field'] = $field;
        $options['data'] = $field->getAttributeValue('value');

        return $options;
    }
}

class_alias(HiddenFieldMapper::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\Mapper\HiddenFieldMapper');
