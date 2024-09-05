<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\ModelBuild;

final class ModelBuildStatus
{
    private string $referenceCode;

    private BuildReportList $buildReports;

    public function __construct(
        string $referenceCode,
        BuildReportList $buildReports
    ) {
        $this->referenceCode = $referenceCode;
        $this->buildReports = $buildReports;
    }

    public function getReferenceCode(): string
    {
        return $this->referenceCode;
    }

    public function getBuildReports(): BuildReportList
    {
        return $this->buildReports;
    }

    /**
     * @param array{
     *     'referenceCode': string,
     *     'buildReports': array<array{
     *          'queueTime': ?string,
     *          'startTime': ?string,
     *          'finishTime': ?string,
     *          'numberOfItems': int,
     *          'taskUuid': ?string,
     *          'state': string,
     *      }>,
     * } $properties
     *
     * @throws \Exception
     */
    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['referenceCode'],
            new BuildReportList(self::extractBuildReports($properties['buildReports'])),
        );
    }

    /**
     * @param array<array{
     *     'queueTime': ?string,
     *     'startTime': ?string,
     *     'finishTime': ?string,
     *     'numberOfItems': int,
     *     'taskUuid': ?string,
     *     'state': string,
     * }> $buildReports
     *
     * @return array<\Ibexa\Personalization\Value\ModelBuild\BuildReport>
     *
     * @throws \Exception
     */
    private static function extractBuildReports(array $buildReports): array
    {
        $reports = [];

        foreach ($buildReports as $buildReport) {
            $reports[] = BuildReport::fromArray($buildReport);
        }

        return $reports;
    }
}
