<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\MediaComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult;
use Ibexa\VersionComparison\Result\Value\BooleanComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\BinaryFileDiff;
use Ibexa\VersionComparison\Result\Value\Diff\BooleanDiff;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

class MediaTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezmedia';
    }

    public function dataToCompare(): array
    {
        $mp4Path = __DIR__ . '/../_fixtures/media/harvester.mp4';
        $webmPath = __DIR__ . '/../_fixtures/media/landscape.webm';
        $webmPath2 = __DIR__ . '/../_fixtures/media/landscape_copy.webm';

        return [
            'same_file' => [
                [
                    'inputUri' => $webmPath,
                    'fileName' => basename($webmPath),
                    'fileSize' => filesize($webmPath),
                    'hasController' => false,
                    'autoplay' => false,
                    'loop' => false,
                    'width' => 0,
                    'height' => 0,
                    'uri' => '',
                ],
                [
                    'inputUri' => $webmPath,
                    'fileName' => basename($webmPath),
                    'fileSize' => filesize($webmPath),
                    'hasController' => false,
                    'autoplay' => false,
                    'loop' => false,
                    'width' => 0,
                    'height' => 0,
                    'uri' => '',
                ],
                new NoComparisonResult(),
            ],
            'no_file' => [
                null,
                null,
                new NoComparisonResult(),
            ],
            'changed_file_and_controls' => [
                [
                    'inputUri' => $webmPath,
                    'fileName' => basename($webmPath),
                    'fileSize' => filesize($webmPath),
                    'hasController' => true,
                    'autoplay' => true,
                    'loop' => true,
                ],
                [
                    'inputUri' => $mp4Path,
                    'fileName' => basename($mp4Path),
                    'fileSize' => filesize($mp4Path),
                    'hasController' => false,
                    'autoplay' => true,
                    'loop' => true,
                ],
                new MediaComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'landscape.webm'),
                        new TokenStringDiff(DiffStatus::ADDED, 'harvester.mp4'),
                    ]),
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, filesize($webmPath)),
                        new IntegerDiff(DiffStatus::ADDED, filesize($mp4Path))
                    ),
                    new BinaryFileComparisonResult(
                        new BinaryFileDiff(
                            DiffStatus::REMOVED,
                            'video/cbe13e669dc5600b366ae9fe0db499f7.webm',
                            '/var/ibexa_demo_site/storage/original/video/cbe13e669dc5600b366ae9fe0db499f7.webm'
                        ),
                        new BinaryFileDiff(
                            DiffStatus::ADDED,
                            'video/cbe13e669dc5600b366ae9fe0db499f7.webm',
                            '/var/ibexa_demo_site/storage/original/video/cbe13e669dc5600b366ae9fe0db499f7.webm'
                        )
                    ),
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::UNCHANGED, true),
                        new BooleanDiff(DiffStatus::UNCHANGED, true)
                    ),
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::REMOVED, true),
                        new BooleanDiff(DiffStatus::ADDED, false)
                    ),
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::UNCHANGED, true),
                        new BooleanDiff(DiffStatus::UNCHANGED, true)
                    ),
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'video/webm'),
                        new TokenStringDiff(DiffStatus::ADDED, 'video/mp4'),
                    ])
                ),
            ],
            'same_file_different_filename' => [
                [
                    'inputUri' => $webmPath,
                    'fileName' => basename($webmPath),
                    'fileSize' => filesize($webmPath),
                    'hasController' => true,
                    'autoplay' => false,
                    'loop' => true,
                ],
                [
                    'inputUri' => $webmPath2,
                    'fileName' => basename($webmPath2),
                    'fileSize' => filesize($webmPath2),
                    'hasController' => true,
                    'autoplay' => false,
                    'loop' => true,
                ],
                new MediaComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'landscape.webm'),
                        new TokenStringDiff(DiffStatus::ADDED, 'landscape_copy.webm'),
                    ]),
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::UNCHANGED, filesize($webmPath)),
                        new IntegerDiff(DiffStatus::UNCHANGED, filesize($webmPath2))
                    ),
                    new BinaryFileComparisonResult(
                        new BinaryFileDiff(
                            DiffStatus::UNCHANGED,
                            'video/cbe13e669dc5600b366ae9fe0db499f7.webm',
                            '/var/ibexa_demo_site/storage/original/video/cbe13e669dc5600b366ae9fe0db499f7.webm'
                        ),
                        new BinaryFileDiff(
                            DiffStatus::UNCHANGED,
                            'video/cbe13e669dc5600b366ae9fe0db499f7.webm',
                            '/var/ibexa_demo_site/storage/original/video/cbe13e669dc5600b366ae9fe0db499f7.webm'
                        )
                    ),
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::UNCHANGED, false),
                        new BooleanDiff(DiffStatus::UNCHANGED, false)
                    ),
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::UNCHANGED, true),
                        new BooleanDiff(DiffStatus::UNCHANGED, true)
                    ),
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::UNCHANGED, true),
                        new BooleanDiff(DiffStatus::UNCHANGED, true)
                    ),
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::UNCHANGED, 'video/webm'),
                    ])
                ),
            ],
            'file_removed' => [
                [
                    'inputUri' => $webmPath,
                    'fileName' => basename($webmPath),
                    'fileSize' => filesize($webmPath),
                    'hasController' => true,
                    'autoplay' => true,
                    'loop' => true,
                ],
                null,
                new MediaComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'landscape.webm'),
                    ]),
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, filesize($webmPath)),
                        new IntegerDiff(DiffStatus::ADDED, null)
                    ),
                    new BinaryFileComparisonResult(
                        new BinaryFileDiff(
                            DiffStatus::REMOVED,
                            'video/cbe13e669dc5600b366ae9fe0db499f7.webm',
                            '/var/ibexa_demo_site/storage/original/video/cbe13e669dc5600b366ae9fe0db499f7.webm'
                        ),
                        new BinaryFileDiff(
                            DiffStatus::ADDED,
                            null,
                            null
                        )
                    ),
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::REMOVED, true),
                        new BooleanDiff(DiffStatus::ADDED, null)
                    ),
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::REMOVED, true),
                        new BooleanDiff(DiffStatus::ADDED, null)
                    ),
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::REMOVED, true),
                        new BooleanDiff(DiffStatus::ADDED, null)
                    ),
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'video/webm'),
                    ])
                ),
            ],
        ];
    }

    /**
     * @param \Ibexa\VersionComparison\Result\FieldType\MediaComparisonResult $expected
     * @param \Ibexa\VersionComparison\Result\FieldType\MediaComparisonResult $actual
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
            $expected->getFileSizeComparisonResult(),
            $actual->getFileSizeComparisonResult()
        );
        $this->assertEquals(
            $expected->getHasControllerComparisonResult(),
            $actual->getHasControllerComparisonResult()
        );
        $this->assertEquals(
            $expected->getAutoplayComparisonResult(),
            $actual->getAutoplayComparisonResult()
        );
        $this->assertEquals(
            $expected->getLoopComparisonResult(),
            $actual->getLoopComparisonResult()
        );
        $this->assertEquals(
            $expected->getMimeTypeComparisonResult(),
            $actual->getMimeTypeComparisonResult()
        );

        $expectedFileResult = $expected->getFileComparisonResult();
        $actualFileResult = $actual->getFileComparisonResult();

        $this->assertEquals(
            $expectedFileResult->getFrom()->getStatus(),
            $actualFileResult->getFrom()->getStatus()
        );
        $this->assertEquals(
            $expectedFileResult->getTo()->getStatus(),
            $actualFileResult->getTo()->getStatus()
        );
    }
}

class_alias(MediaTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\MediaTest');
