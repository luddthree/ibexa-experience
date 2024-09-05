<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\CountryComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\CollectionComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;

class CountryTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezcountry';
    }

    protected function buildFieldDefinitionCreateStruct(string $fieldType): FieldDefinitionCreateStruct
    {
        $titleFieldCreate = $this->contentTypeService->newFieldDefinitionCreateStruct($fieldType, $fieldType);
        $titleFieldCreate->names = [
            'eng-GB' => $fieldType,
        ];
        $titleFieldCreate->fieldSettings = [
            'isMultiple' => true,
        ];

        return $titleFieldCreate;
    }

    public function dataToCompare(): array
    {
        return [
            [
                [
                    'JP',
                    'PL',
                ],
                [
                    'JP',
                    'PL',
                ],
                new NoComparisonResult(),
            ],
            [
                [
                    'JP',
                    'PL',
                ],
                [
                    'DE',
                    'PL',
                ],
                new CountryComparisonResult(
                    new CollectionComparisonResult(
                        new CollectionDiff(
                            DiffStatus::REMOVED,
                            [
                                'JP' => [
                                    'Name' => 'Japan',
                                    'Alpha2' => 'JP',
                                    'Alpha3' => 'JPN',
                                    'IDC' => '81',
                                ],
                            ]
                        ),
                        new CollectionDiff(
                            DiffStatus::UNCHANGED,
                            [
                                'PL' => [
                                    'Name' => 'Poland',
                                    'Alpha2' => 'PL',
                                    'Alpha3' => 'POL',
                                    'IDC' => '48',
                                ],
                            ]
                        ),
                        new CollectionDiff(
                            DiffStatus::ADDED,
                            [
                                'DE' => [
                                    'Name' => 'Germany',
                                    'Alpha2' => 'DE',
                                    'Alpha3' => 'DEU',
                                    'IDC' => '49',
                                ],
                            ]
                        )
                    ),
                ),
            ],
            [
                null,
                [
                    'PL',
                ],
                new CountryComparisonResult(
                    new CollectionComparisonResult(
                        new CollectionDiff(
                            DiffStatus::REMOVED,
                            [
                            ]
                        ),
                        new CollectionDiff(
                            DiffStatus::UNCHANGED,
                            [
                            ]
                        ),
                        new CollectionDiff(
                            DiffStatus::ADDED,
                            [
                                'PL' => [
                                    'Name' => 'Poland',
                                    'Alpha2' => 'PL',
                                    'Alpha3' => 'POL',
                                    'IDC' => '48',                                ],
                            ]
                        )
                    ),
                ),
            ],
        ];
    }
}

class_alias(CountryTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\CountryTest');
