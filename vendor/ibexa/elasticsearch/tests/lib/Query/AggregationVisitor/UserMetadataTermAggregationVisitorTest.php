<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\UserMetadataTermAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\UserMetadataTermAggregationVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class UserMetadataTermAggregationVisitorTest extends AbstractAggregationVisitorTest
{
    private const EXAMPLE_AGGREGATION_NAME = 'custom_aggregation';
    private const EXAMPLE_SUPPORTED_TYPE = UserMetadataTermAggregation::OWNER;

    public function dataProviderForTestSupports(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        yield 'true' => [
            new UserMetadataTermAggregation(self::EXAMPLE_AGGREGATION_NAME, UserMetadataTermAggregation::OWNER),
            $emptyLanguageFilter,
            true,
        ];

        yield 'false (invalid aggregation class)' => [
            $this->createMock(Aggregation::class),
            $emptyLanguageFilter,
            false,
        ];

        yield 'false (invalid aggregation type)' => [
            new UserMetadataTermAggregation(self::EXAMPLE_AGGREGATION_NAME, UserMetadataTermAggregation::GROUP),
            $emptyLanguageFilter,
            false,
        ];
    }

    public function dataProviderForTestVisit(): iterable
    {
        yield [
            new UserMetadataTermAggregation(
                self::EXAMPLE_AGGREGATION_NAME,
                UserMetadataTermAggregation::OWNER
            ),
            MockUtils::createEmptyLanguageFilter(),
            [
                'terms' => [
                    'field' => self::EXAMPLE_SEARCH_INDEX_FIELD,
                    'size' => 10,
                    'min_doc_count' => 1,
                ],
            ],
        ];
    }

    protected function createVisitor(): AggregationVisitor
    {
        return new UserMetadataTermAggregationVisitor(
            self::EXAMPLE_SUPPORTED_TYPE,
            self::EXAMPLE_SEARCH_INDEX_FIELD
        );
    }
}

class_alias(UserMetadataTermAggregationVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\UserMetadataTermAggregationVisitorTest');
