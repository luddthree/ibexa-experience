<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\VersionComparison\FieldValueDiff;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\ImageComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\BinaryFileDiff;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

/**
 * @phpstan-type TTestValue array{
 *     path: string,
 *     alternativeText?: string
 * }|null
 */
final class ImageTest extends AbstractFieldTypeComparisonTest
{
    private const PATH_STANDARD_LOGO = __DIR__ . '/../_fixtures/images/1234aaaa1234-Standard-logo.jpg';
    private const PATH_STANDARD_LOGO2 = __DIR__ . '/../_fixtures/images/1234aaaa1234-Standard-logo2.jpg';
    private const PATH_DARK_LOGO = __DIR__ . '/../_fixtures/images/1234aaaa1234-Dark-logo.png';

    protected function getFieldType(): string
    {
        return 'ezimage';
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

    public function testCompareSameImage(): void
    {
        $fieldDiff = $this->getFieldDiff(
            ['path' => self::PATH_STANDARD_LOGO],
            ['path' => self::PATH_STANDARD_LOGO]
        )['fieldDiff'];

        $this->assertComparisonResult(new NoComparisonResult(), $fieldDiff->getComparisonResult());
    }

    public function testCompareNoImage(): void
    {
        $fieldDiff = $this->getFieldDiff(
            ['path' => null],
            ['path' => null]
        )['fieldDiff'];

        $this->assertComparisonResult(new NoComparisonResult(), $fieldDiff->getComparisonResult());
    }

    public function testCompareNewImage(): void
    {
        $result = $this->getFieldDiff(
            ['path' => null],
            ['path' => self::PATH_STANDARD_LOGO]
        );

        $fieldDiff = $result['fieldDiff'];
        $to = $result['to'];

        $this->assertComparisonResult(new ImageComparisonResult(
            new StringComparisonResult([
                new TokenStringDiff(DiffStatus::ADDED, '1234aaaa1234-Standard-logo.jpg'),
            ]),
            new StringComparisonResult([
                new TokenStringDiff(DiffStatus::ADDED, ''),
            ]),
            new BinaryFileComparisonResult(
                new BinaryFileDiff(
                    DiffStatus::REMOVED,
                    null
                ),
                new BinaryFileDiff(
                    DiffStatus::ADDED,
                    $to->value->id,
                    $to->value->uri
                )
            )
        ), $fieldDiff->getComparisonResult());
    }

    public function testCompareDifferentImage(): void
    {
        $result = $this->getFieldDiff(
            ['path' => self::PATH_STANDARD_LOGO],
            ['path' => self::PATH_STANDARD_LOGO2]
        );

        $fieldDiff = $result['fieldDiff'];
        $from = $result['from'];
        $to = $result['to'];

        $this->assertComparisonResult(new ImageComparisonResult(
            new StringComparisonResult([
                new TokenStringDiff(DiffStatus::REMOVED, '1234aaaa1234-Standard-logo.jpg'),
                new TokenStringDiff(DiffStatus::ADDED, '1234aaaa1234-Standard-logo2.jpg'),
            ]),
            new StringComparisonResult([
                new TokenStringDiff(DiffStatus::UNCHANGED, ''),
            ]),
            new BinaryFileComparisonResult(
                new BinaryFileDiff(
                    DiffStatus::UNCHANGED,
                    $from->value->id,
                    $from->value->uri,
                ),
                new BinaryFileDiff(
                    DiffStatus::UNCHANGED,
                    $to->value->id,
                    $to->value->uri,
                )
            )
        ), $fieldDiff->getComparisonResult());
    }

    public function testCompareDifferentImageWithAlternativeText(): void
    {
        $result = $this->getFieldDiff(
            ['path' => self::PATH_STANDARD_LOGO, 'alternativeText' => 'Standard logo'],
            ['path' => self::PATH_DARK_LOGO, 'alternativeText' => 'Dark logo']
        );

        $fieldDiff = $result['fieldDiff'];
        $from = $result['from'];
        $to = $result['to'];

        $this->assertComparisonResult(new ImageComparisonResult(
            new StringComparisonResult([
                new TokenStringDiff(DiffStatus::REMOVED, '1234aaaa1234-Standard-logo.jpg'),
                new TokenStringDiff(DiffStatus::ADDED, '1234aaaa1234-Dark-logo.png'),
            ]),
            new StringComparisonResult([
                new TokenStringDiff(DiffStatus::REMOVED, 'Standard '),
                new TokenStringDiff(DiffStatus::ADDED, 'Dark '),
                new TokenStringDiff(DiffStatus::UNCHANGED, 'logo'),
            ]),
            new BinaryFileComparisonResult(
                new BinaryFileDiff(
                    DiffStatus::REMOVED,
                    $from->value->id,
                    $from->value->uri
                ),
                new BinaryFileDiff(
                    DiffStatus::ADDED,
                    $to->value->id,
                    $to->value->uri
                )
            )
        ), $fieldDiff->getComparisonResult());
    }

    /**
     * @phpstan-param TTestValue $from
     * @phpstan-param TTestValue $to
     *
     * @phpstan-return array{
     *     fieldDiff: FieldValueDiff,
     *     from: Field|null,
     *     to: Field|null
     * }
     */
    private function getFieldDiff(?array $from, ?array $to): array
    {
        $valueA = $this->getImageValue($from['path'], $from['alternativeText'] ?? null);
        $valueB = $this->getImageValue($to['path'], $to['alternativeText'] ?? null);

        $contentA = $this->createContentA($valueA);
        $contentB = $this->createContentB($contentA, $valueB);

        $versionDiff = $this->versionComparisonService->compare(
            $contentA->versionInfo,
            $contentB->versionInfo,
            'eng-US'
        );

        $fieldIdentifier = $this->getFieldType();

        return [
            'fieldDiff' => $versionDiff->getFieldValueDiffByIdentifier($fieldIdentifier),
            'from' => $contentA->getField($fieldIdentifier),
            'to' => $contentB->getField($fieldIdentifier),
        ];
    }

    /**
     * @phpstan-return array{
     *     path: string,
     *     fileName: string,
     *     fileSize: string,
     *     uri: string,
     *     alternativeText: string
     * }|null
     */
    protected function getImageValue(?string $imagePath, ?string $alternativeText = null): ?array
    {
        if (null === $imagePath) {
            return null;
        }

        $pathInfo = pathinfo($imagePath);

        $value = [
            'path' => $imagePath,
            'fileName' => $pathInfo['basename'],
            'fileSize' => filesize($imagePath),
            'uri' => 'https://' . $imagePath,
        ];

        if (null !== $alternativeText) {
            $value['alternativeText'] = $alternativeText;
        }

        return $value;
    }
}

class_alias(ImageTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\ImageTest');
