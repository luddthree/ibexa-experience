<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\Engine;

use Ibexa\Core\IO\IOService;
use Ibexa\Core\IO\Values\BinaryFile;
use Ibexa\VersionComparison\ComparisonValue\BinaryFileComparisonValue;
use Ibexa\VersionComparison\Engine\Value\BinaryFileComparisonEngine;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\BinaryFileDiff;
use PHPUnit\Framework\TestCase;

class BinaryFileComparisonEngineTest extends TestCase
{
    /** @var \Ibexa\VersionComparison\Engine\Value\BinaryFileComparisonEngine */
    private $engine;

    /** @var \Ibexa\Core\IO\IOService|\PHPUnit\Framework\MockObject\MockObject */
    private $ioServiceMock;

    protected function setUp(): void
    {
        $this->ioServiceMock = $this->createMock(IOService::class);

        $this->engine = new BinaryFileComparisonEngine(
            $this->ioServiceMock
        );
    }

    public function filesAndResultProvider(): array
    {
        $standardLogoPath = __DIR__ . '/../_fixtures/images/Standard-logo.jpg';
        $standardLogoPath2 = __DIR__ . '/../_fixtures/images/Standard-logo2.jpg';
        $darkLogoPath = __DIR__ . '/../_fixtures/images/Dark-logo.png';

        return [
            'value_did_not_change' => [
                new BinaryFileComparisonValue([
                    'id' => 'Standard-logo.jpg',
                    'path' => $standardLogoPath,
                    'size' => filesize($standardLogoPath),
                ]),
                new BinaryFileComparisonValue([
                    'id' => 'Standard-logo.jpg',
                    'path' => $standardLogoPath,
                    'size' => filesize($standardLogoPath),
                ]),
                new BinaryFileComparisonResult(
                    new BinaryFileDiff(DiffStatus::UNCHANGED, 'Standard-logo.jpg', $standardLogoPath),
                    new BinaryFileDiff(DiffStatus::UNCHANGED, 'Standard-logo.jpg', $standardLogoPath)
                ),
            ],
            'same_file_different_name' => [
                new BinaryFileComparisonValue([
                    'id' => 'Standard-logo.jpg',
                    'path' => $standardLogoPath,
                    'size' => filesize($standardLogoPath),
                ]),
                new BinaryFileComparisonValue([
                    'id' => 'Standard-logo2.jpg',
                    'path' => $standardLogoPath2,
                    'size' => filesize($standardLogoPath2),
                ]),
                new BinaryFileComparisonResult(
                    new BinaryFileDiff(DiffStatus::UNCHANGED, 'Standard-logo.jpg', $standardLogoPath),
                    new BinaryFileDiff(DiffStatus::UNCHANGED, 'Standard-logo2.jpg', $standardLogoPath2)
                ),
            ],
            'different_file' => [
                new BinaryFileComparisonValue([
                    'id' => 'Standard-logo.jpg',
                    'path' => $standardLogoPath,
                    'size' => filesize($standardLogoPath),
                ]),
                new BinaryFileComparisonValue([
                    'id' => 'Dark-logo.png',
                    'path' => $darkLogoPath,
                    'size' => filesize($darkLogoPath),
                ]),
                new BinaryFileComparisonResult(
                    new BinaryFileDiff(DiffStatus::REMOVED, 'Standard-logo.jpg', $standardLogoPath),
                    new BinaryFileDiff(DiffStatus::ADDED, 'Dark-logo.png', $darkLogoPath)
                ),
            ],
        ];
    }

    /**
     * @dataProvider filesAndResultProvider
     */
    public function testCompareFieldsData(
        BinaryFileComparisonValue $fileA,
        BinaryFileComparisonValue $fileB,
        BinaryFileComparisonResult $expected
    ): void {
        $this->ioServiceMock
            ->method('loadBinaryFile')
            ->willReturn(
                new BinaryFile(),
            );

        $this->ioServiceMock
            ->method('getFileInputStream')
            ->willReturnOnConsecutiveCalls(
                fopen($fileA->path, 'rb'),
                fopen($fileB->path, 'rb'),
            );

        $this->assertEquals(
            $expected,
            $this->engine->compareValues(
                $fileA,
                $fileB,
            )
        );
    }
}

class_alias(BinaryFileComparisonEngineTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\Engine\BinaryFileComparisonEngineTest');
