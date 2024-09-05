<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\CDP\Export\Content\FieldValueProcessor\FieldType;

use Ibexa\Cdp\Test\Export\Content\FieldValueProcessor\AbstractFieldValueProcessorTest;
use Ibexa\Contracts\Cdp\Export\Content\FieldValueProcessorInterface;
use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\MeasurementTypeFactoryInterface;
use Ibexa\Core\FieldType\Author\Type;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Ibexa\Measurement\CDP\Export\Content\FieldValueProcessor\FieldType\SimpleMeasurementFieldValueProcessor;
use Ibexa\Measurement\FieldType\MeasurementType;
use Ibexa\Measurement\FieldType\MeasurementValue;
use Ibexa\Measurement\Value\Definition\GenericMeasurementType;
use Ibexa\Measurement\Value\Definition\GenericUnit;
use Ibexa\Measurement\Value\SimpleValue;

final class SimpleMeasurementFieldValueProcessorTest extends AbstractFieldValueProcessorTest
{
    public function createFieldValueProcessor(): FieldValueProcessorInterface
    {
        return new SimpleMeasurementFieldValueProcessor(
            $this->createFieldTypeMock(Type::class, 'ibexa_measurement'),
            $this->createMock(MeasurementServiceInterface::class),
            $this->createMock(MeasurementTypeFactoryInterface::class),
        );
    }

    public function providerForTestProcess(): iterable
    {
        $contentType = new ContentType([
            'fieldDefinitions' => new FieldDefinitionCollection([
                new FieldDefinition([
                    'identifier' => 'measurement',
                    'validatorConfiguration' => [
                        'MeasurementValidator' => [
                            'measurementType' => 'simple',
                            'measurementUnit' => 'millimeter',
                        ],
                    ],
                ]),
            ]),
        ]);

        $content = new Content(['contentType' => $contentType]);

        $millimeterUnit = new GenericUnit(
            'millimeter',
            'mm',
            true,
        );
        $centimeterUnit = new GenericUnit(
            'centimeter',
            'cm',
            false,
        );

        $lengthMeasurementType = new GenericMeasurementType(
            'length',
            [
                'millimeter' => $millimeterUnit,
                'centimeter' => $centimeterUnit,
            ],
            $millimeterUnit,
        );

        $measurementTypeFactory = $this->createMock(MeasurementTypeFactoryInterface::class);
        $measurementTypeFactory
            ->method('buildType')
            ->with('simple')
            ->willReturn($lengthMeasurementType);

        yield 'length, 10.0, mm' => [
            new SimpleMeasurementFieldValueProcessor(
                $this->createFieldTypeMock(Type::class, 'ibexa_measurement'),
                $this->createMock(MeasurementServiceInterface::class),
                $measurementTypeFactory,
            ),
            $this->createField(
                'measurement',
                'ibexa_measurement',
                new MeasurementValue(
                    new SimpleValue(
                        $lengthMeasurementType,
                        $millimeterUnit,
                        10.00,
                    ),
                ),
            ),
            $content,
            [
                'value_measurement' => 'length',
                'value_unit_identifier' => 'millimeter',
                'value_unit_symbol' => 'mm',
                'value_unit_is_base' => true,
                'value_base_unit_identifier' => 'millimeter',
                'value_base_unit_symbol' => 'mm',
                'value_simple' => 10.0,
                'value_simple_base_unit' => 10.0,
            ],
        ];

        $measurementValueInBaseUnit = new SimpleValue(
            $lengthMeasurementType,
            $millimeterUnit,
            10.0,
        );

        $measurementService = $this->createMock(MeasurementServiceInterface::class);
        $measurementService
            ->method('convert')
            ->willReturn($measurementValueInBaseUnit);

        $simpleMeasurementFieldValueProcessor = new SimpleMeasurementFieldValueProcessor(
            $this->createFieldTypeMock(Type::class, 'ibexa_measurement'),
            $measurementService,
            $measurementTypeFactory,
        );

        yield 'length, 1.0, cm' => [
            $simpleMeasurementFieldValueProcessor,
            $this->createField(
                'measurement',
                'ibexa_measurement',
                new MeasurementValue(
                    new SimpleValue(
                        $lengthMeasurementType,
                        $centimeterUnit,
                        1.0,
                    ),
                ),
            ),
            $content,
            [
                'value_measurement' => 'length',
                'value_unit_identifier' => 'centimeter',
                'value_unit_symbol' => 'cm',
                'value_unit_is_base' => false,
                'value_base_unit_identifier' => 'millimeter',
                'value_base_unit_symbol' => 'mm',
                'value_simple' => 1.0,
                'value_simple_base_unit' => 10.0,
            ],
        ];

        yield 'no value' => [
            $simpleMeasurementFieldValueProcessor,
            $this->createField(
                'measurement',
                'ibexa_measurement',
                new MeasurementValue(),
            ),
            $content,
            [
                'value_measurement' => 'length',
                'value_unit_identifier' => null,
                'value_unit_symbol' => null,
                'value_unit_is_base' => null,
                'value_base_unit_identifier' => 'millimeter',
                'value_base_unit_symbol' => 'mm',
                'value_simple' => null,
                'value_simple_base_unit' => null,
            ],
        ];
    }

    public function providerForTestSupports(): iterable
    {
        $fieldDefinitionCollection = new FieldDefinitionCollection([
            new FieldDefinition([
                'identifier' => 'measurement_simple',
                'validatorConfiguration' => [
                    'MeasurementValidator' => [
                        'inputType' => MeasurementType::INPUT_TYPE_SIMPLE_INPUT,
                    ],
                ],
            ]),
            new FieldDefinition([
                'identifier' => 'measurement_range',
                'validatorConfiguration' => [
                    'MeasurementValidator' => [
                        'inputType' => MeasurementType::INPUT_TYPE_RANGE,
                    ],
                ],
            ]),
        ]);

        $contentType = new ContentType(['fieldDefinitions' => $fieldDefinitionCollection]);
        $content = new Content(['contentType' => $contentType]);

        yield 'ibexa_measurement, simple, supported' => [
            null,
            $this->createField(
                'measurement_simple',
                'ibexa_measurement',
                $this->createMock(MeasurementValue::class),
            ),
            $content,
            true,
        ];

        yield 'ibexa_measurement, range, not supported' => [
            null,
            $this->createField(
                'measurement_range',
                'ibexa_measurement',
                $this->createMock(MeasurementValue::class),
            ),
            $content,
            false,
        ];

        yield 'not supported generic Value' => [
            null,
            $this->createField(
                'incompatible',
                'incompatible',
                $this->createMock(ValueInterface::class),
            ),
            $content,
            false,
        ];
    }
}
