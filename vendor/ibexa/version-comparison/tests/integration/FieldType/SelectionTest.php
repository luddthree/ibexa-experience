<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\SelectionComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\CollectionComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;

class SelectionTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezselection';
    }

    protected function buildFieldDefinitionCreateStruct(string $fieldType): FieldDefinitionCreateStruct
    {
        $titleFieldCreate = $this->contentTypeService->newFieldDefinitionCreateStruct($fieldType, $fieldType);
        $titleFieldCreate->names = [
            'eng-GB' => $fieldType,
        ];
        $titleFieldCreate->fieldSettings = [
            'isMultiple' => true,
            'options' => [
                1 => 'First',
                2 => 'Second',
                3 => 'Third',
                4 => 'Fourth',
            ],
        ];

        return $titleFieldCreate;
    }

    public function dataToCompare(): array
    {
        return [
            [
                [1, 2],
                [1, 2],
                new NoComparisonResult(),
            ],
            [
                [1, 2],
                [2, 3],
                new SelectionComparisonResult(
                    new CollectionComparisonResult(
                        new CollectionDiff(
                            DiffStatus::REMOVED,
                            [1]
                        ),
                        new CollectionDiff(
                            DiffStatus::UNCHANGED,
                            [2]
                        ),
                        new CollectionDiff(
                            DiffStatus::ADDED,
                            [3]
                        )
                    ),
                ),
            ],
            [
                null,
                [2, 3],
                new SelectionComparisonResult(
                    new CollectionComparisonResult(
                        new CollectionDiff(
                            DiffStatus::REMOVED,
                            []
                        ),
                        new CollectionDiff(
                            DiffStatus::UNCHANGED,
                            []
                        ),
                        new CollectionDiff(
                            DiffStatus::ADDED,
                            [2, 3]
                        )
                    ),
                ),
            ],
        ];
    }
}

class_alias(SelectionTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\SelectionTest');
