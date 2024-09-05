<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value;

use Ibexa\Core\IO\IOServiceInterface;
use Ibexa\VersionComparison\ComparisonValue\BinaryFileComparisonValue;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\BinaryFileDiff;

final class BinaryFileComparisonEngine
{
    private const READ_LENGTH = 2048;

    /** @var \Ibexa\Core\IO\IOServiceInterface */
    private $ioService;

    public function __construct(IOServiceInterface $ioService)
    {
        $this->ioService = $ioService;
    }

    public function compareValues(
        BinaryFileComparisonValue $fileA,
        BinaryFileComparisonValue $fileB
    ): BinaryFileComparisonResult {
        $statusA = $statusB = DiffStatus::UNCHANGED;
        if (!$this->areEqual($fileA, $fileB)) {
            $statusA = DiffStatus::REMOVED;
            $statusB = DiffStatus::ADDED;
        }

        return new BinaryFileComparisonResult(
            new BinaryFileDiff(
                $statusA,
                $fileA->id,
                $fileA->path
            ),
            new BinaryFileDiff(
                $statusB,
                $fileB->id,
                $fileB->path
            ),
        );
    }

    public function areEqual(
        BinaryFileComparisonValue $fileA,
        BinaryFileComparisonValue $fileB
    ): bool {
        // Early optimization
        if ($fileA->size !== $fileB->size) {
            return false;
        }

        if ($fileA->id === $fileB->id) {
            return true;
        }

        if ($fileA->id === null || $fileB->id === null) {
            return false;
        }

        $streamA = $this->ioService->getFileInputStream(
            $this->ioService->loadBinaryFile($fileA->id)
        );
        $streamB = $this->ioService->getFileInputStream(
            $this->ioService->loadBinaryFile($fileB->id)
        );

        while (!feof($streamA) and !feof($streamB)) {
            if (fread($streamA, self::READ_LENGTH) !== fread($streamB, self::READ_LENGTH)) {
                return false;
            }
        }

        return true;
    }
}

class_alias(BinaryFileComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\BinaryFileComparisonEngine');
