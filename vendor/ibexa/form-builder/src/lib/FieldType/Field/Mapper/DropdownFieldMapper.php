<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;

class DropdownFieldMapper extends GenericFieldMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapFormOptions(Field $field, array $constraints): array
    {
        $attributeOptionsValue = $field->getAttributeValue('options');

        $options = parent::mapFormOptions($field, $constraints);
        $options['field'] = $field;
        $options['label'] = $field->getName();
        if ($field->hasAttribute('help')) {
            $options['help'] = $field->getAttributeValue('help');
        }
        $options['choices'] = $this->prepareChoices(
            empty($attributeOptionsValue)
                ? '[]'
                : $attributeOptionsValue
        );

        return $options;
    }

    /**
     * @param string $options
     *
     * @return array
     */
    protected function prepareChoices(string $options)
    {
        $list = json_decode($options);

        $choices = [];
        foreach ($list as $choice) {
            $choices[$choice] = $choice;
        }

        return $choices;
    }
}

class_alias(DropdownFieldMapper::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\Mapper\DropdownFieldMapper');
