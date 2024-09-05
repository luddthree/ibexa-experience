<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\MapLocationDistance;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Value\MapLocationValue;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\ExistsQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\GeoDistanceQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Query;

final class MapLocationDistanceRangeVisitor implements CriterionVisitor
{
    private const SUPPORTED_OPERATORS = [
        Operator::LTE,
        Operator::LT,
        Operator::GT,
        Operator::GTE,
        Operator::BETWEEN,
    ];

    /** @var \Ibexa\Core\Search\Common\FieldNameResolver */
    private $fieldNameResolver;

    /** @var string */
    private $fieldTypeIdentifier;

    /** @var string */
    private $fieldName;

    public function __construct(FieldNameResolver $fieldNameResolver, string $fieldTypeIdentifier, string $fieldName)
    {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->fieldName = $fieldName;
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof MapLocationDistance
            && in_array($criterion->operator, self::SUPPORTED_OPERATORS, true);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Value\MapLocationValue $location */
        $location = $criterion->valueData;

        $searchFields = $this->fieldNameResolver->getFieldTypes(
            $criterion,
            $criterion->target,
            $this->fieldTypeIdentifier,
            $this->fieldName
        );

        if (empty($searchFields)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable Fields found for the provided Criterion target '{$criterion->target}'."
            );
        }

        switch ($criterion->operator) {
            case Operator::LT:
            case Operator::LTE:
                return $this->createLtGeoDistanceQuery(
                    $searchFields,
                    $location,
                    $criterion->value
                )->toArray();
            case Operator::GT:
            case Operator::GTE:
                return $this->createGtGeoDistanceQuery(
                    $searchFields,
                    $location,
                    $criterion->value
                )->toArray();
            case Operator::BETWEEN:
                return $this->createBetweenGeoDistanceQuery(
                    $searchFields,
                    $location,
                    $criterion->value[0],
                    $criterion->value[1]
                )->toArray();
        }

        throw new InvalidArgumentException(
            '$criterion->operator',
            sprintf(
                'Unsupported operator: "%s". Supported operators are: "%s"',
                $criterion->operator,
                implode(', ', self::SUPPORTED_OPERATORS)
            )
        );
    }

    private function createLtGeoDistanceQuery(
        array $searchFields,
        MapLocationValue $location,
        float $distance
    ): Query {
        $query = new BoolQuery();
        foreach ($searchFields as $fieldName => $fieldType) {
            $distanceQuery = new GeoDistanceQuery();
            $distanceQuery->withFieldName($fieldName);
            $distanceQuery->withLat($location->latitude);
            $distanceQuery->withLon($location->longitude);
            $distanceQuery->withDistance($distance . 'km');

            $andQuery = new BoolQuery();
            $andQuery->addMust($distanceQuery);
            $andQuery->addMust(new ExistsQuery($fieldName));

            $query->addShould($andQuery);
        }

        return $query;
    }

    private function createGtGeoDistanceQuery(
        array $searchFields,
        MapLocationValue $location,
        float $distance
    ): Query {
        $query = new BoolQuery();
        foreach ($searchFields as $fieldName => $fieldType) {
            $distanceQuery = new GeoDistanceQuery();
            $distanceQuery->withFieldName($fieldName);
            $distanceQuery->withLat($location->latitude);
            $distanceQuery->withLon($location->longitude);
            $distanceQuery->withDistance($distance . 'km');

            $andQuery = new BoolQuery();
            $andQuery->addMustNot($distanceQuery);
            $andQuery->addMust(new ExistsQuery($fieldName));

            $query->addShould($andQuery);
        }

        return $query;
    }

    private function createBetweenGeoDistanceQuery(
        array $searchFields,
        MapLocationValue $location,
        float $a,
        float $b
    ): Query {
        $query = new BoolQuery();
        $query->addMust($this->createGtGeoDistanceQuery($searchFields, $location, $a));
        $query->addMust($this->createLtGeoDistanceQuery($searchFields, $location, $b));

        return $query;
    }
}

class_alias(MapLocationDistanceRangeVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\MapLocationDistanceRangeVisitor');
