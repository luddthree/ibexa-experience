<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper;

use Ibexa\ConnectorQualifio\FieldTypePage\Form\Type\BlockAttribute\AttributeQualifioChannelListType;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Symfony\Component\Form\FormBuilderInterface;

final class QualifioChannelListFormTypeMapper implements AttributeFormTypeMapperInterface
{
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        return $formBuilder->create(
            'value',
            AttributeQualifioChannelListType::class,
            [
                'constraints' => $constraints,
            ]
        );
    }
}
