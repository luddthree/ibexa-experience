<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value;

use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;
use SebastianBergmann\Diff\Differ;

final class StringComparisonEngine
{
    /** @var \SebastianBergmann\Diff\Differ */
    private $innerEngine;

    /** @var int */
    private const DIFF_TOKEN = 0;

    /** @var int */
    private const DIFF_STATUS = 1;

    public function __construct()
    {
        $this->innerEngine = new Differ();
    }

    public function compareValues(
        StringComparisonValue $stringA,
        StringComparisonValue $stringB
    ): StringComparisonResult {
        if ($stringA->value === $stringB->value) {
            return new StringComparisonResult([
                new TokenStringDiff(
                    DiffStatus::UNCHANGED,
                    $stringA->value
                ),
            ]);
        }

        if (($stringA->value === '' && $stringB->value !== '')
            || $stringA->value === null && $stringB->value !== null) {
            return new StringComparisonResult([
                new TokenStringDiff(
                    DiffStatus::ADDED,
                    $stringB->value
                ),
            ]);
        }

        if (($stringA->value !== '' && $stringB->value === '')
            || $stringA->value !== null && $stringB->value === null) {
            return new StringComparisonResult([
                new TokenStringDiff(
                    DiffStatus::REMOVED,
                    $stringA->value
                ),
            ]);
        }

        $rawDiff = $this->innerEngine->diffToArray(
            $stringA->doNotSplit ? [$stringA->value] : preg_split($stringA->splitBy, $stringA->value, -1),
            $stringB->doNotSplit ? [$stringB->value] : preg_split($stringB->splitBy, $stringB->value, -1)
        );

        $combinedDiff = $this->combineValuesWithSameStatus($rawDiff);

        $tokensDiff = [];
        foreach ($combinedDiff as $diff) {
            $tokensDiff[] = new TokenStringDiff(
                $this->mapStatus($diff[1]),
                $diff[0]
            );
        }

        return new StringComparisonResult($tokensDiff);
    }

    private function mapStatus(int $status): string
    {
        switch ($status) {
            case Differ::ADDED:
                return DiffStatus::ADDED;
            case Differ::REMOVED:
                return DiffStatus::REMOVED;
            default:
                return DiffStatus::UNCHANGED;
        }
    }

    private function combineValuesWithSameStatus(array $rawDiff): array
    {
        return array_reduce($rawDiff, static function (array $combined, array $diff): array {
            $key = array_key_last($combined);

            if ($key === null || $diff[self::DIFF_STATUS] !== $combined[$key][self::DIFF_STATUS]) {
                $combined[] = [
                    $diff[self::DIFF_TOKEN],
                    $diff[self::DIFF_STATUS],
                ];
            } else {
                $combined[$key][self::DIFF_TOKEN] .= $diff[self::DIFF_TOKEN];
            }

            return $combined;
        }, []);
    }
}

class_alias(StringComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\StringComparisonEngine');
