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
use Ibexa\VersionComparison\Engine\Value\HashMapComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\CountryComparisonResult;

final class CountryComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\HashMapComparisonEngine */
    private $hashMapComparisonEngine;

    public function __construct(HashMapComparisonEngine $hashMapComparisonEngine)
    {
        $this->hashMapComparisonEngine = $hashMapComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Country\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Country\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        return new CountryComparisonResult(
            $this->hashMapComparisonEngine->compareValues(
                $comparisonDataA->countries,
                $comparisonDataB->countries
            )
        );
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Country\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Country\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return $comparisonDataA->countries->hashMap !== $comparisonDataB->countries->hashMap;
    }
}

class_alias(CountryComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\CountryComparisonEngine');
