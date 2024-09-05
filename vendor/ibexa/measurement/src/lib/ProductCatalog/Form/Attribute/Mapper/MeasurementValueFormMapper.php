<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Mapper;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Measurement\FieldType\MeasurementType;
use Symfony\Component\Form\FormBuilderInterface;

final class MeasurementValueFormMapper implements ValueFormMapperInterface
{
    private RangeMeasurementValueFormMapper $rangeMeasurementValueFormMapper;

    private SingleMeasurementValueFormMapper $singleMeasurementValueFormMapper;

    public function __construct(
        RangeMeasurementValueFormMapper $rangeMeasurementValueFormMapper,
        SingleMeasurementValueFormMapper $singleMeasurementValueFormMapper
    ) {
        $this->rangeMeasurementValueFormMapper = $rangeMeasurementValueFormMapper;
        $this->singleMeasurementValueFormMapper = $singleMeasurementValueFormMapper;
    }

    public function createValueForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $context = []
    ): void {
        $definition = $assignment->getAttributeDefinition();
        $definitionOptions = $definition->getOptions();

        $inputType = $definitionOptions->get('inputType');

        switch ($inputType) {
            case MeasurementType::INPUT_TYPE_SIMPLE_INPUT:
                $this->singleMeasurementValueFormMapper->createValueForm($name, $builder, $assignment, $context);

                return;
            case MeasurementType::INPUT_TYPE_RANGE:
                $this->rangeMeasurementValueFormMapper->createValueForm($name, $builder, $assignment, $context);

                return;
        }
    }
}
