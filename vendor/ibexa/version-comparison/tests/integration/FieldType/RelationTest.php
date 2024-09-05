<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\RelationComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;

class RelationTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezobjectrelation';
    }

    public function dataToCompare(): array
    {
        self::markTestSkipped('Logic moved to separate test cases.');
    }

    /**
     * @dataProvider dataToCompare
     */
    public function testCompareVersions($oldValue, $newValue, ComparisonResult $expectedComparisonResult): void
    {
        self::markTestSkipped('Logic moved to separate test cases.');
    }

    public function testCompareEqualVersions(): void
    {
        $contentId = $this->createContentForRelationTarget()->id;

        parent::testCompareVersions($contentId, $contentId, new NoComparisonResult());
    }

    public function testCompareChangedVersions(): void
    {
        $oldContentId = $this->createContentForRelationTarget()->id;
        $newContentId = $this->createContentForRelationTarget()->id;

        parent::testCompareVersions($oldContentId, $newContentId, new RelationComparisonResult(
            new IntegerComparisonResult(
                new IntegerDiff(DiffStatus::REMOVED, $oldContentId),
                new IntegerDiff(DiffStatus::ADDED, $newContentId)
            )
        ));
    }

    public function testCompareAddedVersions(): void
    {
        $newContentId = $this->createContentForRelationTarget()->id;

        parent::testCompareVersions(null, $newContentId, new RelationComparisonResult(
            new IntegerComparisonResult(
                new IntegerDiff(DiffStatus::REMOVED, null),
                new IntegerDiff(DiffStatus::ADDED, $newContentId)
            )
        ));
    }

    public function testCompareRemovedVersions(): void
    {
        $oldContentId = $this->createContentForRelationTarget()->id;

        parent::testCompareVersions($oldContentId, null, new RelationComparisonResult(
            new IntegerComparisonResult(
                new IntegerDiff(DiffStatus::REMOVED, $oldContentId),
                new IntegerDiff(DiffStatus::ADDED, null)
            )
        ));
    }

    protected function createContentForRelationTarget(): Content
    {
        $contentOneDraft = $this->createContentDraft(
            'folder',
            2,
            [
                'name' => 'relation_test',
            ]
        );

        return $this->contentService->publishVersion($contentOneDraft->getVersionInfo());
    }
}

class_alias(RelationTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\RelationTest');
