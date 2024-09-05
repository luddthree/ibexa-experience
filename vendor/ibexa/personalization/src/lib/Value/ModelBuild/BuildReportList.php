<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\ModelBuild;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use Webmozart\Assert\Assert;

/**
 * @implements IteratorAggregate<array-key, \Ibexa\Personalization\Value\ModelBuild\BuildReport>
 */
final class BuildReportList implements IteratorAggregate, Countable
{
    /** @var array<\Ibexa\Personalization\Value\ModelBuild\BuildReport> */
    private array $buildReports;

    /**
     * @param array<\Ibexa\Personalization\Value\ModelBuild\BuildReport> $buildReports
     */
    public function __construct(array $buildReports)
    {
        Assert::allIsInstanceOf($buildReports, BuildReport::class);

        $this->buildReports = $buildReports;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->buildReports);
    }

    public function getLastBuildReport(): ?BuildReport
    {
        $lastBuildReport = reset($this->buildReports);

        return false !== $lastBuildReport ? $lastBuildReport : null;
    }

    public function count(): int
    {
        return count($this->buildReports);
    }
}
