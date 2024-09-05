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
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

class GenericChoiceFormTypeMapper implements AttributeFormTypeMapperInterface
{
    /** @var string */
    private $formTypeClass;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface */
    private $blockDefinitionFactory;

    public function __construct(string $formTypeClass, BlockDefinitionFactoryInterface $blockDefinitionFactory)
    {
        $this->formTypeClass = $formTypeClass;
        $this->blockDefinitionFactory = $blockDefinitionFactory;
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
        $options = $blockAttributeDefinition->getOptions();

        $choices = $options['choices'] ?? [];

        return $formBuilder->create(
            'value',
            $this->formTypeClass,
            [
                'choices' => $choices,
                'constraints' => $constraints,
            ]
        );
    }
}

class_alias(GenericChoiceFormTypeMapper::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\FormTypeMapper\GenericChoiceFormTypeMapper');
