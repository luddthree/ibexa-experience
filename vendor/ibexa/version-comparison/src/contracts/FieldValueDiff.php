<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\VersionComparison;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;

class FieldValueDiff extends ValueObject
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition */
    private $fieldDefinition;

    /** @var \Ibexa\Contracts\VersionComparison\Result\ComparisonResult */
    private $comparisonResult;

    public function __construct(
        FieldDefinition $fieldDefinition,
        ComparisonResult $comparisonResult
    ) {
        $this->fieldDefinition = $fieldDefinition;
        $this->comparisonResult = $comparisonResult;
    }

    public function getComparisonResult(): ComparisonResult
    {
        return $this->comparisonResult;
    }

    public function getFieldDefinition(): FieldDefinition
    {
        return $this->fieldDefinition;
    }

    public function isChanged(): bool
    {
        return $this->comparisonResult->isChanged();
    }
}

class_alias(FieldValueDiff::class, 'EzSystems\EzPlatformVersionComparison\FieldValueDiff');
