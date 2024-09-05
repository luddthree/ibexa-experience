<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\Form\Type\BlockAttribute\NestedAttributeType;
use RuntimeException;
use Symfony\Component\Form\FormBuilderInterface;

final class NestedAttributeFormTypeMapper implements AttributeFormTypeMapperInterface
{
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        $options = $blockAttributeDefinition->getOptions();
        if (!isset($options['attributes'])) {
            throw new RuntimeException('Missing configured attributes under \'options\' key');
        }

        $attributeTypes = array_column($options['attributes'], 'type');
        if (in_array('nested_attribute', $attributeTypes, true)) {
            throw new RuntimeException('Could not use nested_attribute in group');
        }

        // Users are allowed to set one of nested attribute as required.
        // To make validation work nested_attribute field needs to be set as required as well
        if ($this->isNestedAttributeRequired($blockAttributeDefinition)) {
            $formBuilder->setRequired(true);
        }

        $isMultiple = $options['multiple'] ?? false;

        return $formBuilder->create(
            'value',
            NestedAttributeType::class,
            [
                'constraints' => $constraints,
                'allow_add' => $isMultiple,
                'allow_delete' => $isMultiple,
                'entry_options' => [
                    'allow_delete' => $isMultiple,
                    'attributes' => $options['attributes'],
                    'block_definition' => $blockDefinition,
                    'block_attribute_definition' => $blockAttributeDefinition,
                    'language_code' => $formBuilder->getForm()->getConfig()->getOption('language_code'),
                ],
                'compound' => true,
            ]
        );
    }

    private function isNestedAttributeRequired(BlockAttributeDefinition $blockAttributeDefinition): bool
    {
        $attributes = $blockAttributeDefinition->getOptions()['attributes'] ?? [];

        $notBlankValidators = array_filter(
            array_column($attributes, 'validators'),
            static function (array $validator): bool {
                return array_key_exists('not_blank', $validator)
                    || array_key_exists('not_blank_richtext', $validator);
            }
        );

        return !empty($notBlankValidators);
    }
}
