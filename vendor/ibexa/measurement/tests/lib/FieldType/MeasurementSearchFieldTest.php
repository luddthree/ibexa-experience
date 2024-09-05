<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\FieldType;

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;
use Ibexa\Contracts\Core\Search\FieldType;
use Ibexa\Measurement\FieldType\MeasurementSearchField;
use PHPUnit\Framework\TestCase;

final class MeasurementSearchFieldTest extends TestCase
{
    private MeasurementSearchField $searchField;

    protected function setUp(): void
    {
        $this->searchField = new MeasurementSearchField();
    }

    /**
     * @return array<string, array<int, array<\Ibexa\Contracts\Core\Search\Field>|\Ibexa\Contracts\Core\Persistence\Content\Field>>
     */
    public function getDataForTestGetIndexData(): iterable
    {
        yield 'simple value' => [
            new Field([
                'value' => new FieldValue(
                    [
                        'data' => [
                            'inputType' => 0,
                            'measurementType' => 'mass',
                            'measurementUnit' => 'kilogram',
                            'value' => 12.5,
                        ],
                    ]
                ),
            ]),
            array_merge(
                $this->getCommonSearchFields('simple'),
                [
                    new Search\Field(
                        'value',
                        12.5,
                        new Search\FieldType\FloatField()
                    ),
                    new Search\Field(
                        'fulltext',
                        'mass: 12.500000 kilogram',
                        new Search\FieldType\FullTextField()
                    ),
                ]
            ),
        ];

        yield 'range value' => [
            new Field([
                'value' => new FieldValue(
                    [
                        'data' => [
                            'inputType' => 1,
                            'measurementType' => 'mass',
                            'measurementUnit' => 'kilogram',
                            'measurementRangeMinimumValue' => 10,
                            'measurementRangeMaximumValue' => 12.5,
                        ],
                    ]
                ),
            ]),
            array_merge(
                $this->getCommonSearchFields('range'),
                [
                    new Search\Field(
                        'min_value',
                        10,
                        new Search\FieldType\FloatField()
                    ),
                    new Search\Field(
                        'max_value',
                        12.5,
                        new Search\FieldType\FloatField()
                    ),
                    new Search\Field(
                        'fulltext',
                        'mass: 10.000000 - 12.500000 kilogram',
                        new Search\FieldType\FullTextField()
                    ),
                ]
            ),
        ];
    }

    /**
     * @dataProvider getDataForTestGetIndexData
     *
     * @param array<\Ibexa\Contracts\Core\Search\Field> $expectedSearchFields
     */
    public function testGetIndexData(Field $field, array $expectedSearchFields): void
    {
        $fieldDefinition = new FieldDefinition();

        self::assertEquals(
            $expectedSearchFields,
            $this->searchField->getIndexData($field, $fieldDefinition)
        );
    }

    public function testGetDefaultMatchField(): void
    {
        self::assertSame('value', $this->searchField->getDefaultMatchField());
    }

    public function testGetDefaultSortField(): void
    {
        self::assertSame('value', $this->searchField->getDefaultSortField());
    }

    public function testGetIndexDefinition(): void
    {
        foreach ($this->searchField->getIndexDefinition() as $key => $value) {
            self::assertIsString($key);
            self::assertInstanceOf(FieldType::class, $value);
        }
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Search\Field>
     */
    private function getCommonSearchFields(string $valueType): array
    {
        return [
            new Search\Field(
                'type',
                'mass',
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'unit',
                'kilogram',
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'value_type',
                $valueType,
                new Search\FieldType\StringField()
            ),
        ];
    }
}
