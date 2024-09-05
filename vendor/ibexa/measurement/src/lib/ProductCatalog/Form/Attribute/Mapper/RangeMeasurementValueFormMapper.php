<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Mapper;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Measurement\ProductCatalog\Form\Type\RangeAttributeMeasurementType;
use Symfony\Component\Form\FormBuilderInterface;

final class RangeMeasurementValueFormMapper extends AbstractMeasurementValueFormMapper
{
    protected function buildForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $options
    ): void {
        $definition = $assignment->getAttributeDefinition();
        $definitionOptions = $definition->getOptions();

        /** @var array{'defaultRangeMinimumValue'?: float|null, 'defaultRangeMaximumValue'?: float|null} $rangeMinMax */
        $rangeMinMax = $definitionOptions->get('measurementDefaultRange');
        $rangeMin = isset($rangeMinMax['defaultRangeMinimumValue']) ? (float) $rangeMinMax['defaultRangeMinimumValue'] : null;
        $rangeMax = isset($rangeMinMax['defaultRangeMaximumValue']) ? (float) $rangeMinMax['defaultRangeMaximumValue'] : null;

        $options = array_merge($options, [
            'defaultRangeMinimumValue' => $rangeMin,
            'defaultRangeMaximumValue' => $rangeMax,
        ]);

        $builder->add($name, RangeAttributeMeasurementType::class, $options);
    }
}
