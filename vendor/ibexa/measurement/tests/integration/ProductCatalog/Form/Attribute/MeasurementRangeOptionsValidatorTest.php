<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Form\Attribute;

use Ibexa\Measurement\ProductCatalog\Form\Attribute\MeasurementRangeOptionsValidator;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;

/**
 * @covers \Ibexa\Measurement\ProductCatalog\Form\Attribute\MeasurementRangeOptionsValidator
 */
final class MeasurementRangeOptionsValidatorTest extends AbstractMeasurementOptionsValidatorTest
{
    protected function getValidator(): MeasurementRangeOptionsValidator
    {
        return self::getServiceByClassName(MeasurementRangeOptionsValidator::class);
    }

    public function testValidateEmptyOptions(): void
    {
        $errors = $this->getValidator()->validateOptions(new AttributeDefinitionOptions([]));

        self::assertErrorExists($errors, '[type]', 'This field is missing.');
        self::assertErrorExists($errors, '[unit]', 'This field is missing.');
        self::assertCount(2, $errors);
    }

    public function provideForValidOptions(): iterable
    {
        yield 'Only required properties' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
            ],
        ];

        yield 'Range value, only required properties' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
            ],
        ];

        yield 'Range value, null values for minimum/maximum' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'measurementRange' => [
                    'minimum' => null,
                    'maximum' => null,
                ],
                'measurementDefaultRange' => [
                    'defaultRangeMinimumValue' => null,
                    'defaultRangeMaximumValue' => null,
                ],
            ],
        ];

        yield 'Range value, all options defined' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'measurementRange' => [
                    'minimum' => 0,
                    'maximum' => 100,
                ],
                'measurementDefaultRange' => [
                    'defaultRangeMinimumValue' => 0,
                    'defaultRangeMaximumValue' => 100,
                ],
            ],
        ];
    }

    public function provideForInvalidOptions(): iterable
    {
        yield 'Non existent type' => [
            [
                'type' => 'foo',
                'unit' => 'bar',
            ],
            [
                ['target' => '[type]', 'message' => 'Type does not exist'],
            ],
        ];

        yield 'Non existent unit in type' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => 'foo',
            ],
            [
                ['target' => '[unit]', 'message' => 'Unit does not exist in type'],
            ],
        ];

        yield 'Range value, default ranges exceed allowed values' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'measurementRange' => [
                    'minimum' => 10,
                    'maximum' => 10,
                ],
                'measurementDefaultRange' => [
                    'defaultRangeMinimumValue' => 0,
                    'defaultRangeMaximumValue' => 100,
                ],
            ],
            [
                ['target' => '[measurementDefaultRange][defaultRangeMinimumValue]', 'message' => 'This value should be greater than or equal to 10.'],
                ['target' => '[measurementDefaultRange][defaultRangeMaximumValue]', 'message' => 'This value should be less than or equal to 10.'],
            ],
        ];

        yield 'Range value, ranges are in wrong order' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'measurementRange' => [
                    'minimum' => 20,
                    'maximum' => 10,
                ],
                'measurementDefaultRange' => [
                    'defaultRangeMinimumValue' => 30,
                    'defaultRangeMaximumValue' => 0,
                ],
            ],
            [
                ['target' => '[measurementRange][minimum]', 'message' => 'This value should be less than or equal to 10.'],
                ['target' => '[measurementRange][maximum]', 'message' => 'This value should be greater than or equal to 20.'],
                ['target' => '[measurementDefaultRange][defaultRangeMinimumValue]', 'message' => 'This value should be less than or equal to 0.'],
                ['target' => '[measurementDefaultRange][defaultRangeMinimumValue]', 'message' => 'This value should be less than or equal to 10.'],
                ['target' => '[measurementDefaultRange][defaultRangeMaximumValue]', 'message' => 'This value should be greater than or equal to 30.'],
                ['target' => '[measurementDefaultRange][defaultRangeMaximumValue]', 'message' => 'This value should be greater than or equal to 20.'],
            ],
        ];
    }
}
