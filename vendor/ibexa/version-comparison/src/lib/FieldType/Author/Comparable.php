<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\Author;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\Collection;
use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;

final class Comparable implements ComparableInterface
{
    /**
     * @param \Ibexa\Core\FieldType\Author\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        $authors = [];

        foreach ($value->authors as $author) {
            /** @var \Ibexa\Core\FieldType\Author\Author $author */
            $authors[] = new Author([
                'id' => (int)$author->id,
                'name' => new StringComparisonValue([
                    'value' => $author->name,
                ]),
                'email' => new StringComparisonValue([
                    'value' => $author->email,
                ]),
            ]);
        }

        return new Value([
            'authors' => new Collection([
                'collection' => $authors,
                'compareCallable' => static function (Author $authorA, Author $authorB): int {
                    return $authorA->id <=> $authorB->id;
                },
            ]),
        ]);
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Author\Comparable');
