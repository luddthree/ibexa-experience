<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\PageBuilder\FormTypeMapper;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Segmentation\Form\Type\AttributeTargetedContentMapType;
use Symfony\Component\Form\FormBuilderInterface;

class SegmentContentMapFormTypeMapper implements AttributeFormTypeMapperInterface
{
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        return $formBuilder->create(
            'value',
            AttributeTargetedContentMapType::class,
            [
                'constraints' => $constraints,
            ]
        );
    }
}

class_alias(SegmentContentMapFormTypeMapper::class, 'Ibexa\Platform\Segmentation\PageBuilder\FormTypeMapper\SegmentContentMapFormTypeMapper');
