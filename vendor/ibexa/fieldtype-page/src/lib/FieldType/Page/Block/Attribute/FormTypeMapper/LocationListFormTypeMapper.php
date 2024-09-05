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
use Ibexa\FieldTypePage\Form\Type\BlockAttribute\AttributeLocationListType;
use Symfony\Component\Form\FormBuilderInterface;

class LocationListFormTypeMapper implements AttributeFormTypeMapperInterface
{
    private BlockDefinitionFactoryInterface $blockDefinitionFactory;

    public function __construct(BlockDefinitionFactoryInterface $blockDefinitionFactory)
    {
        $this->blockDefinitionFactory = $blockDefinitionFactory;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $blockAttributeDefinition
     * @param array $constraints
     *
     * @return \Symfony\Component\Form\FormBuilderInterface
     *
     * @throws \Exception
     */
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        $collectionBlockDefinition = $this->blockDefinitionFactory->getBlockDefinition('collection');

        $locationListAttribute = $collectionBlockDefinition->getAttributes()['locationlist'];

        return $formBuilder->create(
            'value',
            AttributeLocationListType::class,
            [
                'match' => $locationListAttribute->getOptions()['match'],
                'constraints' => $constraints,
            ]
        );
    }
}

class_alias(LocationListFormTypeMapper::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\FormTypeMapper\LocationListFormTypeMapper');
