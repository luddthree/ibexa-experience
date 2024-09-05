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
use Symfony\Component\Form\FormBuilderInterface;

class GenericFormTypeMapper implements AttributeFormTypeMapperInterface
{
    /** @var class-string */
    private $formTypeClass;

    private bool $mergeOptions;

    /** @var array<string>|null */
    private ?array $optionsAllowList;

    /**
     * GenericFormTypeMapper constructor.
     *
     * $optionsAllowList describes what options should be passed into OptionsResolver for the form field.
     * If null and $mergeOptions = true, all options will be passed.
     *
     * @phpstan-param class-string $formTypeClass
     * @phpstan-param array<string>|null $optionsAllowList
     */
    public function __construct(
        string $formTypeClass,
        bool $mergeOptions = false,
        ?array $optionsAllowList = null
    ) {
        $this->formTypeClass = $formTypeClass;
        $this->mergeOptions = $mergeOptions;
        $this->optionsAllowList = $optionsAllowList;
    }

    /**
     * @param array<\Symfony\Component\Validator\Constraint> $constraints
     */
    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        $options = $this->mergeOptions ? $blockAttributeDefinition->getOptions() : [];
        if ($this->optionsAllowList !== null) {
            $options = array_filter(
                $options,
                fn (string $key): bool => in_array($key, $this->optionsAllowList, true),
                ARRAY_FILTER_USE_KEY,
            );
        }
        $options['constraints'] = $constraints;

        return $formBuilder->create(
            'value',
            $this->formTypeClass,
            $options,
        );
    }
}

class_alias(GenericFormTypeMapper::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\FormTypeMapper\GenericFormTypeMapper');
