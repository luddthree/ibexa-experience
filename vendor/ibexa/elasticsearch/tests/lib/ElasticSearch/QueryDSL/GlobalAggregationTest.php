<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Aggregation;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\GlobalAggregation;
use PHPUnit\Framework\TestCase;
use stdClass;

final class GlobalAggregationTest extends TestCase
{
    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(GlobalAggregation $aggregation, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $aggregation->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        $inner = $this->createAggregationMock(['terms' => ['foo' => 'bar']]);

        yield 'constructor' => [
            new GlobalAggregation(['inner' => $inner]),
            [
                'global' => new stdClass(),
                'aggs' => [
                    'inner' => [
                        'terms' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
        ];

        yield 'setters' => [
            (new GlobalAggregation())->addAggregation('inner', $inner),
            [
                'global' => new stdClass(),
                'aggs' => [
                    'inner' => [
                        'terms' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function createAggregationMock(array $data): Aggregation
    {
        $aggregation = $this->createMock(Aggregation::class);
        $aggregation->method('toArray')->willReturn($data);

        return $aggregation;
    }
}

class_alias(GlobalAggregationTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\GlobalAggregationTest');
