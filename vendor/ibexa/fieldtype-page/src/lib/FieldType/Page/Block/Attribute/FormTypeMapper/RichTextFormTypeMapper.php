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
use Ibexa\FieldTypeRichText\Validator\Constraints\RichText as IsValidRichText;
use Symfony\Component\Form\FormBuilderInterface;

final class RichTextFormTypeMapper implements AttributeFormTypeMapperInterface
{
    /** @var string */
    private $formTypeClass;

    /**
     * @param string $formTypeClass
     */
    public function __construct(string $formTypeClass)
    {
        $this->formTypeClass = $formTypeClass;
    }

    /**
     * {@inheritdoc}
     */
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        $constraints[] = new IsValidRichText();

        return $formBuilder->create(
            'value',
            $this->formTypeClass,
            [
                'constraints' => $constraints,
                'language_code' => $formBuilder->getForm()->getConfig()->getOption('language_code'),
            ]
        );
    }
}

class_alias(RichTextFormTypeMapper::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\FormTypeMapper\RichTextFormTypeMapper');
