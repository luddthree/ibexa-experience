<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver;

use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver\ContentFieldAggregationFieldResolver;
use Ibexa\Tests\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver\Stub\FieldAggregationStub;
use PHPUnit\Framework\TestCase;

final class ContentFieldAggregationFieldResolverTest extends TestCase
{
    private const EXAMPLE_CONTENT_TYPE_IDENTIFIER = 'product';
    private const EXAMPLE_FIELD_DEFINITION_IDENTIFIER = 'price';

    private const EXAMPLE_SEARCH_INDEX_FIELD = 'product_price_f';

    public function testResolveTargetField(): void
    {
        $aggregation = new FieldAggregationStub(
            self::EXAMPLE_CONTENT_TYPE_IDENTIFIER,
            self::EXAMPLE_FIELD_DEFINITION_IDENTIFIER
        );

        $fieldNameResolver = $this->createMock(FieldNameResolver::class);
        $fieldNameResolver->method('getAggregationFieldName')
            ->with(
                self::EXAMPLE_CONTENT_TYPE_IDENTIFIER,
                self::EXAMPLE_FIELD_DEFINITION_IDENTIFIER,
                'value'
            )
            ->willReturn(self::EXAMPLE_SEARCH_INDEX_FIELD);

        $resolver = new ContentFieldAggregationFieldResolver($fieldNameResolver, 'value');

        $this->assertEquals(
            self::EXAMPLE_SEARCH_INDEX_FIELD,
            $resolver->resolveTargetField($aggregation)
        );
    }
}

class_alias(ContentFieldAggregationFieldResolverTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\AggregationFieldResolver\ContentFieldAggregationFieldResolverTest');
