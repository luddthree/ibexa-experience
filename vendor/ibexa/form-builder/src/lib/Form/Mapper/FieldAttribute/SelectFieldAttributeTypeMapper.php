<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Mapper\FieldAttribute;

use Ibexa\FieldTypePage\Form\Type\BlockAttribute\AttributeSelectType;
use Ibexa\FormBuilder\Definition\FieldAttributeDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;

class SelectFieldAttributeTypeMapper implements AttributeConfigurationMapperInterface
{
    /** @var string */
    private $typeIdentifier;

    /**
     * @param string $typeIdentifier
     */
    public function __construct(string $typeIdentifier)
    {
        $this->typeIdentifier = $typeIdentifier;
    }

    /**
     * @return string
     */
    public function getTypeIdentifier(): string
    {
        return $this->typeIdentifier;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Ibexa\FormBuilder\Definition\FieldDefinition $fieldDefinition
     * @param \Ibexa\FormBuilder\Definition\FieldAttributeDefinition $fieldAttributeDefinition
     * @param array $constraints
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function map(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        FieldAttributeDefinition $fieldAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        $options = $fieldAttributeDefinition->getOptions();

        $choices = $options['choices'] ?? [];
        $multiple = $options['multiple'] ?? false;

        return $formBuilder->create(
            'value',
            AttributeSelectType::class,
            [
                'choices' => $choices,
                'multiple' => $multiple,
                'constraints' => $constraints,
            ]
        );
    }
}

class_alias(SelectFieldAttributeTypeMapper::class, 'EzSystems\EzPlatformFormBuilder\Form\Mapper\FieldAttribute\SelectFieldAttributeTypeMapper');
