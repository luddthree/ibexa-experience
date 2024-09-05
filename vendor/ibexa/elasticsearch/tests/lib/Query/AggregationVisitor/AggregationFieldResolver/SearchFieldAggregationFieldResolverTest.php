<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver\SearchFieldAggregationFieldResolver;
use Ibexa\Tests\Core\Search\TestCase;

final class SearchFieldAggregationFieldResolverTest extends TestCase
{
    private const EXAMPLE_SEARCH_INDEX_FIELD = 'custom_field_id';

    public function testResolveTargetField(): void
    {
        $aggregation = $this->createMock(Aggregation::class);

        $resolver = new SearchFieldAggregationFieldResolver(self::EXAMPLE_SEARCH_INDEX_FIELD);

        $this->assertEquals(
            self::EXAMPLE_SEARCH_INDEX_FIELD,
            $resolver->resolveTargetField($aggregation)
        );
    }
}

class_alias(SearchFieldAggregationFieldResolverTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\AggregationFieldResolver\SearchFieldAggregationFieldResolverTest');
