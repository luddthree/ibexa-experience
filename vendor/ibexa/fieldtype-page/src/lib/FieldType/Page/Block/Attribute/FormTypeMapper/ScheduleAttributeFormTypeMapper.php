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
use Ibexa\FieldTypePage\Form\Type\BlockAttribute\ScheduleAttributeType;
use Symfony\Component\Form\FormBuilderInterface;

class ScheduleAttributeFormTypeMapper implements AttributeFormTypeMapperInterface
{
    /** @var string */
    protected $attributeIdentifier;

    /**
     * @param string $attributeIdentifier
     */
    public function __construct(string $attributeIdentifier)
    {
        $this->attributeIdentifier = $attributeIdentifier;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $blockAttributeDefinition
     * @param array $constraints
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     */
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        $formType = $formBuilder->create(
            'value',
            ScheduleAttributeType::class,
            [
                'constraints' => $constraints,
                'attribute_identifier' => $this->attributeIdentifier,
            ]
        );

        return $formType;
    }
}

class_alias(ScheduleAttributeFormTypeMapper::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\FormTypeMapper\ScheduleAttributeFormTypeMapper');
