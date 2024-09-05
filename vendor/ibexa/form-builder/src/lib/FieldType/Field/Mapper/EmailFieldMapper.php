<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;

class EmailFieldMapper extends GenericFieldMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapFormOptions(Field $field, array $constraints): array
    {
        $options = parent::mapFormOptions($field, $constraints);
        $options['field'] = $field;
        $options['label'] = $field->getName();
        if ($field->hasAttribute('help')) {
            $options['help'] = $field->getAttributeValue('help');
        }

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

class_alias(EmailFieldMapper::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\Mapper\EmailFieldMapper');
