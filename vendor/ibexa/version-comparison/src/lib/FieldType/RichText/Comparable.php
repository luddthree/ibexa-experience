<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\RichText;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\HtmlComparisonValue;

final class Comparable implements ComparableInterface
{
    /** @var \Ibexa\Contracts\FieldTypeRichText\RichText\Converter */
    private $converter;

    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param \Ibexa\FieldTypeRichText\FieldType\RichText\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        return new Value([
            'html' => new HtmlComparisonValue([
                'value' => $this->converter->convert($value->xml)->saveHTML(),
            ]),
        ]);
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\RichText\Comparable');
