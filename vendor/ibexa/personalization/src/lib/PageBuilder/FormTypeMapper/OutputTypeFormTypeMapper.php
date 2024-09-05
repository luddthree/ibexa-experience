<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\PageBuilder\FormTypeMapper;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Personalization\Form\Type\BlockAttribute\OutputTypeListAttributeType;
use Ibexa\Personalization\Validator\Constraints\OutputType;
use Symfony\Component\Form\FormBuilderInterface;

final class OutputTypeFormTypeMapper implements AttributeFormTypeMapperInterface
{
    /**
     * @param array<\Symfony\Component\Validator\Constraint> $constraints
     */
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        return $formBuilder->create(
            'value',
            OutputTypeListAttributeType::class,
            [
                'constraints' => [new OutputType()],
            ]
        );
    }
}
