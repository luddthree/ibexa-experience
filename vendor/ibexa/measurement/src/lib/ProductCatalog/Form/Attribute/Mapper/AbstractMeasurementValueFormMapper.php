<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Mapper;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeValue;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractMeasurementValueFormMapper implements ValueFormMapperInterface
{
    /**
     * @param array<string,mixed> $context
     *
     * @return array<string, mixed>
     */
    protected function getOptionsFromDefinition(
        AttributeDefinitionAssignmentInterface $assignment,
        array $context = []
    ): array {
        $definition = $assignment->getAttributeDefinition();
        $definitionOptions = $definition->getOptions();

        /** @var array{'minimum'?: float|null, 'maximum'?: float|null} $inputMinMax */
        $inputMinMax = $definitionOptions->get('measurementRange');
        $min = isset($inputMinMax['minimum']) ? (float)$inputMinMax['minimum'] : null;
        $max = isset($inputMinMax['maximum']) ? (float)$inputMinMax['maximum'] : null;

        $options = [
            'disabled' => $context['translation_mode'] ?? false,
            'label' => $definition->getName(),
            'block_prefix' => 'measurement_attribute_value',
            'required' => $assignment->isRequired(),
            'constraints' => [
                new AttributeValue([
                    'definition' => $definition,
                ]),
            ],
            'measurementType' => $definitionOptions->get('type'),
            'measurementUnit' => $definitionOptions->get('unit'),
            'minimum' => $min,
            'maximum' => $max,
        ];

        if ($assignment->isRequired()) {
            $options['constraints'][] = new Assert\NotBlank();
        }

        return $options;
    }

    /**
     * @param array<string, mixed> $options
     */
    abstract protected function buildForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $options
    ): void;

    public function createValueForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $context = []
    ): void {
        $options = $this->getOptionsFromDefinition($assignment);
        $this->buildForm($name, $builder, $assignment, $options);
    }
}
