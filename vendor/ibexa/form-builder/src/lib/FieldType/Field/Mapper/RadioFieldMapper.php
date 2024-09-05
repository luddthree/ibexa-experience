<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;

class RadioFieldMapper extends GenericFieldMapper
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
        $options['required'] = true;
        $options['choices'] = $this->prepareChoices(
            empty($attributeOptionsValue)
                ? '[]'
                : $attributeOptionsValue
        );
        $options['data'] = $this->getDefaultChoice($options['choices']);

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

    /**
     * @param array $choices
     *
     * @return string|null
     */
    protected function getDefaultChoice(array $choices): ?string
    {
        $values = array_values($choices);

        return array_shift($values);
    }
}

class_alias(RadioFieldMapper::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\Mapper\RadioFieldMapper');
