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
use Ibexa\VersionComparison\Result\FieldType\RelationListComparisonResult;

final class RelationListComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\CollectionComparisonEngine */
    private $collectionComparisonEngine;

    public function __construct(CollectionComparisonEngine $collectionComparisonEngine)
    {
        $this->collectionComparisonEngine = $collectionComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\RelationList\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\RelationList\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $collectionComparisonResult = $this->collectionComparisonEngine->compareValues(
            $comparisonDataA->relationIdList,
            $comparisonDataB->relationIdList
        );

        return new RelationListComparisonResult($collectionComparisonResult);
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\RelationList\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\RelationList\Value $comparisonDataB
     */
    public function shouldRunComparison(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): bool {
        return $comparisonDataA->relationIdList->collection !== $comparisonDataB->relationIdList->collection;
    }
}

class_alias(RelationListComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\RelationListComparisonEngine');
