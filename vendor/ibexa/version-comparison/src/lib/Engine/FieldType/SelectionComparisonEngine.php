<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\FieldType;

use Ibexa\Contracts\VersionComparison\Engine\FieldTypeComparisonEngine;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Engine\Value\CollectionComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\SelectionComparisonResult;

final class SelectionComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine */
    private $collectionComparisonEngine;

    public function __construct(CollectionComparisonEngine $collectionComparisonEngine)
    {
        $this->collectionComparisonEngine = $collectionComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Selection\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Selection\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        return new SelectionComparisonResult(
            $this->collectionComparisonEngine->compareValues(
                $comparisonDataA->selection,
                $comparisonDataB->selection
            )
        );
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Selection\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Selection\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return $comparisonDataA->selection->collection != $comparisonDataB->selection->collection;
    }
}

class_alias(SelectionComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\SelectionComparisonEngine');
