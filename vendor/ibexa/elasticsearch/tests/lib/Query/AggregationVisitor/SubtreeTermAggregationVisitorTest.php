<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Location\SubtreeTermAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Elasticsearch\Query\AggregationVisitor\SubtreeTermAggregationVisitor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class SubtreeTermAggregationVisitorTest extends AbstractAggregationVisitorTest
{
    private const EXAMPLE_AGGREGATION_NAME = 'custom_aggregation';
    private const EXAMPLE_PATH_STRING = '/1/2/';

    private const EXAMPLE_PATH_STRING_FIELD_NAME = 'path_string_id';
    private const EXAMPLE_LOCATION_ID_FIELD_NAME = 'location_id_id';

    public function dataProviderForTestSupports(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        yield 'true' => [
            new SubtreeTermAggregation(
                self::EXAMPLE_AGGREGATION_NAME,
                self::EXAMPLE_PATH_STRING
            ),
            $emptyLanguageFilter,
            true,
        ];

        yield 'false' => [
            $this->createMock(Aggregation::class),
            $emptyLanguageFilter,
            false,
        ];
    }

    public function dataProviderForTestVisit(): iterable
    {
        yield [
            new SubtreeTermAggregation(
                self::EXAMPLE_AGGREGATION_NAME,
                self::EXAMPLE_PATH_STRING
            ),
            MockUtils::createEmptyLanguageFilter(),
            [
                'filter' => [
                    'wildcard' => [
                        self::EXAMPLE_PATH_STRING_FIELD_NAME => [
                            'value' => '\/1\/2\/?*',
                        ],
                    ],
                ],
                'aggs' => [
                    'nested' => [
                        'terms' => [
                            'field' => self::EXAMPLE_LOCATION_ID_FIELD_NAME,
                            'size' => 12,
                            'min_doc_count' => 1,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function createVisitor(): AggregationVisitor
    {
        return new SubtreeTermAggregationVisitor(
            self::EXAMPLE_PATH_STRING_FIELD_NAME,
            self::EXAMPLE_LOCATION_ID_FIELD_NAME
        );
    }
}

class_alias(SubtreeTermAggregationVisitorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\AggregationVisitor\SubtreeTermAggregationVisitorTest');
