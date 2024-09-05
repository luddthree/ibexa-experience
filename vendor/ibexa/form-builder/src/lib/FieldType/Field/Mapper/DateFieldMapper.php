<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field\Mapper;

use DateTime;
use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;

class DateFieldMapper extends GenericFieldMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapFormOptions(Field $field, array $constraints): array
    {
        $defaultValue = null;
        if ((bool)$field->getAttributeValue('current_date_as_default_value')) {
            $defaultValue = new DateTime();
            $defaultValue->setTime(0, 0, 0, 0);
        }

        $options = parent::mapFormOptions($field, $constraints);
        $options['field'] = $field;
        $options['label'] = $field->getName();
        if ($field->hasAttribute('help')) {
            $options['help'] = $field->getAttributeValue('help');
        }
        $options['html5'] = false;
        $options['format'] = $field->getAttributeValue('format');
        $options['data'] = $defaultValue;

        if (
            $field->hasAttribute('placeholder')
            && (string)$field->getAttributeValue('placeholder') !== ''
        ) {
            $options['attr'] = [
                'placeholder' => $field->getAttributeValue('placeholder'),
            ];
        }

        return $options;
    }
}

class_alias(DateFieldMapper::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\Mapper\DateFieldMapper');
