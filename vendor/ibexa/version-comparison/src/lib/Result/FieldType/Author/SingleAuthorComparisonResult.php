<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType\Author;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

final class SingleAuthorComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $nameComparisonResult;

    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $emailComparisonResult;

    /** @var int */
    private $id;

    public function __construct(
        int $id,
        StringComparisonResult $nameComparisonResult,
        StringComparisonResult $emailComparisonResult
    ) {
        $this->nameComparisonResult = $nameComparisonResult;
        $this->emailComparisonResult = $emailComparisonResult;
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNameComparisonResult(
    ): StringComparisonResult {
        return $this->nameComparisonResult;
    }

    public function getEmailComparisonResult(
    ): StringComparisonResult {
        return $this->emailComparisonResult;
    }

    public function isChanged(): bool
    {
        return $this->nameComparisonResult->isChanged() || $this->emailComparisonResult->isChanged();
    }
}

class_alias(SingleAuthorComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\Author\SingleAuthorComparisonResult');
