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
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\GeoDistanceQuery;

final class MapLocationDistanceInVisitor implements CriterionVisitor
{
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
        if ($criterion instanceof MapLocationDistance) {
            return $criterion->operator === Operator::EQ || $criterion->operator === Operator::IN;
        }

        return false;
    }

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

        $qb = new BoolQuery();
        foreach ((array)$criterion->value as $value) {
            foreach ($searchFields as $fieldName => $fieldType) {
                $distanceQb = new GeoDistanceQuery();
                $distanceQb->withFieldName($fieldName);
                $distanceQb->withLat($location->latitude);
                $distanceQb->withLon($location->longitude);
                $distanceQb->withDistance($value . 'km');

                $qb->addShould($distanceQb);
            }
        }

        return $qb->toArray();
    }
}

class_alias(MapLocationDistanceInVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\MapLocationDistanceInVisitor');
