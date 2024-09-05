<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\FieldAggregation;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver;
use RuntimeException;

final class ContentFieldAggregationFieldResolver implements AggregationFieldResolver
{
    /** @var \Ibexa\Core\Search\Common\FieldNameResolver */
    private $fieldNameResolver;

    /** @var string */
    private $searchFieldName;

    public function __construct(FieldNameResolver $fieldNameResolver, string $searchFieldName)
    {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->searchFieldName = $searchFieldName;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\FieldAggregation $aggregation
     */
    public function resolveTargetField(Aggregation $aggregation): string
    {
        if (!($aggregation instanceof FieldAggregation)) {
            throw new RuntimeException('Expected instance of ' . FieldAggregation::class . ' , got ' . get_class($aggregation));
        }

        $searchFieldName = $this->fieldNameResolver->getAggregationFieldName(
            $aggregation->getContentTypeIdentifier(),
            $aggregation->getFieldDefinitionIdentifier(),
            $this->searchFieldName
        );

        if ($searchFieldName === null) {
            throw new RuntimeException('No searchable fields found for the provided aggregation target');
        }

        return $searchFieldName;
    }
}

class_alias(ContentFieldAggregationFieldResolver::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\AggregationFieldResolver\ContentFieldAggregationFieldResolver');
