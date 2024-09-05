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
use Ibexa\FieldTypePage\Form\Type\BlockAttribute\AttributeSelectType;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class SelectFormTypeMapper implements AttributeFormTypeMapperInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private bool $mergeOptions;

    /** @var array<string>|null */
    private ?array $optionsAllowList;

    /**
     * @phpstan-param array<string>|null $optionsAllowList
     */
    public function __construct(
        bool $mergeOptions = false,
        ?array $optionsAllowList = null,
        ?LoggerInterface $logger = null
    ) {
        $this->mergeOptions = $mergeOptions;
        $this->optionsAllowList = $optionsAllowList;
        $this->logger = $logger ?? new NullLogger();
    }

    public function map(
        FormBuilderInterface $formBuilder,
        BlockDefinition $blockDefinition,
        BlockAttributeDefinition $blockAttributeDefinition,
        array $constraints = []
    ): FormBuilderInterface {
        $definitionOptions = $blockAttributeDefinition->getOptions();
        $options = $this->mergeOptions ? $definitionOptions : [];
        if ($this->optionsAllowList !== null) {
            $options = array_filter(
                $options,
                fn (string $key): bool => in_array($key, $this->optionsAllowList, true),
                ARRAY_FILTER_USE_KEY,
            );
        }
        $basicOptions = [
            'constraints' => $constraints,
            'choices' => $definitionOptions['choices'] ?? [],
            'multiple' => $definitionOptions['multiple'] ?? false,
        ];

        try {
            return $formBuilder->create(
                'value',
                AttributeSelectType::class,
                $options,
            );
        } catch (InvalidOptionsException $e) {
            $message = sprintf(
                'Invalid configuration of Page Builder block "%s" attribute "%s". %s.',
                $blockDefinition->getName(),
                $blockAttributeDefinition->getName(),
                $e->getMessage(),
            );
            $this->logger->error($message, [
                'exception' => $e,
            ]);

            return $formBuilder->create(
                'value',
                AttributeSelectType::class,
                $basicOptions,
            );
        }
    }
}

class_alias(SelectFormTypeMapper::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\FormTypeMapper\SelectFormTypeMapper');
