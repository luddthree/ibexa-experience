<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Form\Attribute;

use Ibexa\Measurement\ProductCatalog\Form\Attribute\MeasurementOptionsValidator;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;

/**
 * @covers \Ibexa\Measurement\ProductCatalog\Form\Attribute\MeasurementOptionsValidator
 */
final class MeasurementOptionsValidatorTest extends AbstractMeasurementOptionsValidatorTest
{
    protected function getValidator(): MeasurementOptionsValidator
    {
        return self::getServiceByClassName(MeasurementOptionsValidator::class);
    }

    public function testValidateEmptyOptions(): void
    {
        $errors = $this->getValidator()->validateOptions(new AttributeDefinitionOptions([]));

        self::assertErrorExists($errors, '[type]', 'This field is missing.');
        self::assertErrorExists($errors, '[unit]', 'This field is missing.');
        self::assertErrorExists($errors, '[inputType]', 'This field is missing.');
        self::assertCount(3, $errors);
    }

    public function provideForValidOptions(): iterable
    {
        yield 'Single value, only required properties' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'lte',
            ],
        ];

        yield 'Single value with default' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'lte',
                'defaultValue' => -99,
            ],
        ];

        yield 'Single value, all options defined' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'lte',
                'measurementRange' => [
                    'minimum' => 0,
                    'maximum' => 42,
                ],
                'defaultValue' => 20,
            ],
        ];

        yield 'Range value, only required properties' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 1,
            ],
        ];

        yield 'Range value, null values for minimum/maximum' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 1,
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
                'inputType' => 1,
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
                'inputType' => 1,
            ],
            [
                ['target' => '[type]', 'message' => 'Type does not exist'],
            ],
        ];

        yield 'Non existent unit in type' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => 'foo',
                'inputType' => 1,
            ],
            [
                ['target' => '[unit]', 'message' => 'Unit does not exist in type'],
            ],
        ];

        yield 'Non existent input type' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 2,
            ],
            [
                ['target' => '[inputType]', 'message' => 'The value you selected is not a valid choice.'],
            ],
        ];

        yield 'Single value, missing sign' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
            ],
            [
                ['target' => '[sign]', 'message' => 'This field is missing.'],
            ],
        ];

        yield 'Single value, invalid sign' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'foo',
            ],
            [
                ['target' => '[sign]', 'message' => 'The value you selected is not a valid choice.'],
            ],
        ];

        yield 'Single value, incorrect range constraints' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'lte',
                'measurementRange' => [
                    'minimum' => 42,
                    'maximum' => 0,
                ],
            ],
            [
                ['target' => '[measurementRange][minimum]', 'message' => 'This value should be less than or equal to 0.'],
                ['target' => '[measurementRange][maximum]', 'message' => 'This value should be greater than or equal to 42.'],
            ],
        ];

        yield 'Single value, default value is higher than range' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'lte',
                'measurementRange' => [
                    'minimum' => 0,
                    'maximum' => 42,
                ],
                'defaultValue' => 99,
            ],
            [
                ['target' => '[defaultValue]', 'message' => 'This value should be less than or equal to 42.'],
            ],
        ];

        yield 'Single value, default value is lower than range' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'lte',
                'measurementRange' => [
                    'minimum' => 0,
                    'maximum' => 42,
                ],
                'defaultValue' => -99,
            ],
            [
                ['target' => '[defaultValue]', 'message' => 'This value should be greater than or equal to 0.'],
            ],
        ];

        yield 'Single value, default value is not numeric' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'lte',
                'defaultValue' => 'foo',
            ],
            [
                ['target' => '[defaultValue]', 'message' => 'This value should be of type numeric.'],
            ],
        ];

        yield 'Single value, minimum range is not numeric' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'lte',
                'measurementRange' => [
                    'minimum' => 'foo',
                ],
            ],
            [
                ['target' => '[measurementRange][minimum]', 'message' => 'This value should be of type numeric.'],
            ],
        ];

        yield 'Single value, maximum range is not numeric' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 0,
                'sign' => 'lte',
                'measurementRange' => [
                    'maximum' => 'foo',
                ],
            ],
            [
                ['target' => '[measurementRange][maximum]', 'message' => 'This value should be of type numeric.'],
            ],
        ];

        yield 'Range value, default ranges exceed allowed values' => [
            [
                'type' => self::EXISTING_TYPE,
                'unit' => self::EXISTING_UNIT,
                'inputType' => 1,
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
                'inputType' => 1,
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
