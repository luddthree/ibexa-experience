<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\Core\FieldType\ImageAsset\Value;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\ImageAssetComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\BinaryFileDiff;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

class ImageAssetTest extends AbstractFieldTypeComparisonTest
{
    private const REMOTE_ID_TO_PATH = [
        'standard_logo' => [
            'path' => __DIR__ . '/../_fixtures/images/1234aaaa1234-Standard-logo.jpg',
            'alternativeText' => 'Standard Logo',
        ],
        'standard_logo_2' => [
            'path' => __DIR__ . '/../_fixtures/images/1234aaaa1234-Standard-logo2.jpg',
            'alternativeText' => null,
        ],
        'dark_logo' => [
            'path' => __DIR__ . '/../_fixtures/images/1234aaaa1234-Dark-logo.png',
            'alternativeText' => 'Dark Logo',
        ],
    ];

    private static $remoteIdToContentId = [];

    protected function getFieldType(): string
    {
        return 'ezimageasset';
    }

    /**
     * @dataProvider dataToCompare
     */
    public function testCompareVersions($oldValue, $newValue, ComparisonResult $expectedComparisonResult): void
    {
        foreach (self::REMOTE_ID_TO_PATH as $remoteId => $imageData) {
            self::$remoteIdToContentId[$remoteId] = $this->createImageContent(
                $remoteId,
                $imageData['path'],
                $imageData['alternativeText']
            )->id;
        }

        parent::testCompareVersions(
            new Value(
                self::$remoteIdToContentId[$oldValue['destinationContentId']] ?? null,
                $oldValue['alternativeText']
            ),
            new Value(
                self::$remoteIdToContentId[$newValue['destinationContentId']] ?? null,
                $newValue['alternativeText']
            ),
            $expectedComparisonResult
        );
    }

    public function dataToCompare(): array
    {
        $fakeIntegerDiff = static function (string $status, ?string $remoteId = null): IntegerDiff {
            return new class($status, $remoteId) extends IntegerDiff {
                /** @var string|null */
                public $remoteId;

                public function __construct(string $status, ?string $remoteId = null)
                {
                    $this->status = $status;
                    $this->remoteId = $remoteId;
                }
            };
        };

        return [
            [
                [
                    'destinationContentId' => null,
                    'alternativeText' => null,
                ],
                [
                    'destinationContentId' => null,
                    'alternativeText' => null,
                ],
                new NoComparisonResult(),
            ],
            [
                [
                    'destinationContentId' => 'standard_logo',
                    'alternativeText' => null,
                ],
                [
                    'destinationContentId' => 'standard_logo',
                    'alternativeText' => null,
                ],
                new NoComparisonResult(),
            ],
            [
                [
                    'destinationContentId' => 'standard_logo',
                    'alternativeText' => null,
                ],
                [
                    'destinationContentId' => 'standard_logo_2',
                    'alternativeText' => null,
                ],
                new ImageAssetComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, '1234aaaa1234-Standard-logo.jpg'),
                        new TokenStringDiff(DiffStatus::ADDED, '1234aaaa1234-Standard-logo2.jpg'),
                    ]),
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'Standard Logo'),
                    ]),
                    new IntegerComparisonResult(
                        $fakeIntegerDiff(DiffStatus::REMOVED, 'standard_logo'),
                        $fakeIntegerDiff(DiffStatus::ADDED, 'standard_logo_2')
                    ),
                    new BinaryFileComparisonResult(
                        new BinaryFileDiff(
                            DiffStatus::UNCHANGED,
                            '/var/ibexa_demo_site/storage/images/1/2/2/0/221-1-eng-US/1234aaaa1234-Standard-logo.jpg'
                        ),
                        new BinaryFileDiff(
                            DiffStatus::UNCHANGED,
                            '/var/ibexa_demo_site/storage/images/5/2/2/0/225-2-eng-US/1234aaaa1234-Standard-logo2.jpg2'
                        )
                    )
                ),
            ],
            [
                [
                    'destinationContentId' => 'standard_logo',
                    'alternativeText' => 'Overridden alt text',
                ],
                [
                    'destinationContentId' => 'dark_logo',
                    'alternativeText' => null,
                ],
                new ImageAssetComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, '1234aaaa1234-Standard-logo.jpg'),
                        new TokenStringDiff(DiffStatus::ADDED, '1234aaaa1234-Dark-logo.png'),
                    ]),
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'Overridden alt text'),
                        new TokenStringDiff(DiffStatus::ADDED, 'Dark Logo'),
                    ]),
                    new IntegerComparisonResult(
                        $fakeIntegerDiff(DiffStatus::REMOVED, 'standard_logo'),
                        $fakeIntegerDiff(DiffStatus::ADDED, 'dark_logo')
                    ),
                    new BinaryFileComparisonResult(
                        new BinaryFileDiff(
                            DiffStatus::REMOVED,
                            '/var/ibexa_demo_site/storage/images/2/2/2/0/222-1-eng-US/1234aaaa1234-Standard-logo.jpg'
                        ),
                        new BinaryFileDiff(
                            DiffStatus::ADDED,
                            '/var/ibexa_demo_site/storage/images/8/2/2/0/228-1-eng-US/1234aaaa1234-Standard-logo2.jpg2'
                        )
                    )
                ),
            ],
            [
                [
                    'destinationContentId' => null,
                    'alternativeText' => null,
                ],
                [
                    'destinationContentId' => 'dark_logo',
                    'alternativeText' => 'Override alt text',
                ],
                new ImageAssetComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::ADDED, '1234aaaa1234-Dark-logo.png'),
                    ]),
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::ADDED, 'Override alt text'),
                    ]),
                    new IntegerComparisonResult(
                        $fakeIntegerDiff(DiffStatus::REMOVED, null),
                        $fakeIntegerDiff(DiffStatus::ADDED, 'dark_logo')
                    ),
                    new BinaryFileComparisonResult(
                        new BinaryFileDiff(
                            DiffStatus::REMOVED,
                            null
                        ),
                        new BinaryFileDiff(
                            DiffStatus::ADDED,
                            '/var/ibexa_demo_site/storage/images/8/2/2/0/228-1-eng-US/1234aaaa1234-Standard-logo2.jpg2'
                        )
                    )
                ),
            ],
        ];
    }

    private function createImageContent(string $remoteId, string $path, ?string $alternativeText): Content
    {
        $draft = $this->createContentDraft(
            'image',
            43,
            [
                    'name' => basename($path),
                    'image' => [
                        'path' => $path,
                        'fileName' => basename($path),
                        'fileSize' => filesize($path),
                        'uri' => 'http://' . $path,
                        'alternativeText' => $alternativeText,
                    ],
                ],
            $remoteId
        );

        return $this->contentService->publishVersion($draft->versionInfo);
    }

    /**
     * @param \Ibexa\VersionComparison\Result\FieldType\ImageAssetComparisonResult $expected
     * @param \Ibexa\VersionComparison\Result\FieldType\ImageAssetComparisonResult $actual
     */
    protected function assertComparisonResult(ComparisonResult $expected, ComparisonResult $actual): void
    {
        if ($expected instanceof NoComparisonResult) {
            parent::assertComparisonResult($expected, $actual);

            return;
        }
        $this->assertEquals(
            $expected->getFileNameComparisonResult(),
            $actual->getFileNameComparisonResult()
        );
        $this->assertEquals(
            $expected->getAlternativeTextComparisonResult(),
            $actual->getAlternativeTextComparisonResult()
        );

        $destinationIdResultFrom = $expected->getDestinationIdComparisonResult()->getFrom();
        $destinationIdResultTo = $expected->getDestinationIdComparisonResult()->getTo();

        $this->assertEquals(
            new IntegerComparisonResult(
                new IntegerDiff(
                    $destinationIdResultFrom->getStatus(),
                    self::$remoteIdToContentId[$destinationIdResultFrom->remoteId] ?? null
                ),
                new IntegerDiff(
                    $destinationIdResultTo->getStatus(),
                    self::$remoteIdToContentId[$destinationIdResultTo->remoteId] ?? null
                ),
            ),
            $actual->getDestinationIdComparisonResult()
        );

        $this->assertEquals(
            $expected->getBinaryFileComparisonResult()->getFrom()->getStatus(),
            $actual->getBinaryFileComparisonResult()->getFrom()->getStatus()
        );

        $this->assertEquals(
            $expected->getBinaryFileComparisonResult()->getTo()->getStatus(),
            $actual->getBinaryFileComparisonResult()->getTo()->getStatus()
        );
    }
}

class_alias(ImageAssetTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\ImageAssetTest');
