<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Symfony\Component\Form\FormBuilderInterface;

final class CheckboxFormTypeMapper implements AttributeFormTypeMapperInterface
{
    /** @phpstan-var class-string<\Symfony\Component\Form\FormTypeInterface> */
    private string $formTypeClass;

    /**
     * @phpstan-param class-string<\Symfony\Component\Form\FormTypeInterface> $formTypeClass
     */
    public function __construct(
        string $formTypeClass
    ) {
        $this->formTypeClass = $formTypeClass;
    }

    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        return $formBuilder->create(
            'value',
            $this->formTypeClass,
            [
                'label' => $blockAttributeDefinition->getName(),
                'constraints' => $constraints,
            ]
        );
    }
}
