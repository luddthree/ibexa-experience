<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\SortClauseVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\MapLocationDistance;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Search\Common\FieldNameResolver;

final class MapLocationDistanceVisitor extends AbstractSortClauseVisitor
{
    /** @var \Ibexa\Core\Search\Common\FieldNameResolver */
    private $fieldNameResolver;

    /** @var string */
    private $fieldName;

    public function __construct(FieldNameResolver $fieldNameResolver, string $fieldName)
    {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldName = $fieldName;
    }

    public function supports(SortClause $sortClause, LanguageFilter $languageFilter): bool
    {
        return $sortClause instanceof MapLocationDistance;
    }

    public function visit(SortClauseVisitor $visitor, SortClause $sortClause, LanguageFilter $languageFilter): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Target\MapLocationTarget $target */
        $target = $sortClause->targetData;

        $fieldName = $this->fieldNameResolver->getSortFieldName(
            $sortClause,
            $target->typeIdentifier,
            $target->fieldIdentifier,
            $this->fieldName
        );

        if ($fieldName === null) {
            throw new InvalidArgumentException(
                '$sortClause->targetData',
                'No searchable Fields found for the provided Sort Clause target ' .
                "'{$target->fieldIdentifier}' on '{$target->typeIdentifier}'."
            );
        }

        return [
            '_geo_distance' => [
                $fieldName => [
                    'lat' => $target->latitude,
                    'lon' => $target->longitude,
                ],
                'order' => $this->getDirection($sortClause),
                'unit' => 'km',
                'mode' => 'min',
                'distance_type' => 'arc',
                'ignore_unmapped' => true,
            ],
        ];
    }
}

class_alias(MapLocationDistanceVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\SortClauseVisitor\MapLocationDistanceVisitor');
