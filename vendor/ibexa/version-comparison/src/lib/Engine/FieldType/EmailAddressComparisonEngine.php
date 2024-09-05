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
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\EmailAddressComparisonResult;

final class EmailAddressComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringValueComparisonEngine;

    public function __construct(StringComparisonEngine $stringValueComparisonEngine)
    {
        $this->stringValueComparisonEngine = $stringValueComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\EmailAddress\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\EmailAddress\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        return new EmailAddressComparisonResult(
            $this->stringValueComparisonEngine->compareValues(
                $comparisonDataA->email,
                $comparisonDataB->email
            )
        );
    }

    public function shouldRunComparison(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): bool {
        return $comparisonDataA->email->value !== $comparisonDataB->email->value;
    }
}

class_alias(EmailAddressComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\EmailAddressComparisonEngine');
