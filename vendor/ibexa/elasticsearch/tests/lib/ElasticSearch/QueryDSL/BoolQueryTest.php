<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\Query;
use PHPUnit\Framework\TestCase;

final class BoolQueryTest extends TestCase
{
    private const EXAMPLE_MINIMUM_SHOULD_MATCH = 2;

    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(BoolQuery $query, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $query->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        yield 'constructor' => [
            new BoolQuery(
                [
                    $this->createDummyQuery(['A' => 'A']),
                ],
                [
                    $this->createDummyQuery(['B' => 'B']),
                ],
                [
                    $this->createDummyQuery(['C' => 'C']),
                ],
                [
                    $this->createDummyQuery(['D' => 'D']),
                ],
                self::EXAMPLE_MINIMUM_SHOULD_MATCH
            ),
            [
                'bool' => [
                    'must' => [
                        ['A' => 'A'],
                    ],
                    'filter' => [
                        ['B' => 'B'],
                    ],
                    'should' => [
                        ['C' => 'C'],
                    ],
                    'must_not' => [
                        ['D' => 'D'],
                    ],
                    'minimum_should_match' => self::EXAMPLE_MINIMUM_SHOULD_MATCH,
                ],
            ],
        ];

        $foo = $this->createDummyQuery(['foo' => 'foo']);
        $bar = $this->createDummyQuery(['bar' => 'bar']);
        $baz = $this->createDummyQuery(['baz' => 'baz']);

        yield 'must' => [
            (new BoolQuery())->addMust($foo)->addMust($bar)->addMust($baz),
            [
                'bool' => [
                    'must' => [
                        ['foo' => 'foo'],
                        ['bar' => 'bar'],
                        ['baz' => 'baz'],
                    ],
                ],
            ],
        ];

        yield 'filter' => [
            (new BoolQuery())->addFilter($foo)->addFilter($bar)->addFilter($baz),
            [
                'bool' => [
                    'filter' => [
                        ['foo' => 'foo'],
                        ['bar' => 'bar'],
                        ['baz' => 'baz'],
                    ],
                ],
            ],
        ];

        yield 'should' => [
            (new BoolQuery())->addShould($foo)->addShould($bar)->addShould($baz),
            [
                'bool' => [
                    'should' => [
                        ['foo' => 'foo'],
                        ['bar' => 'bar'],
                        ['baz' => 'baz'],
                    ],
                    'minimum_should_match' => 1,
                ],
            ],
        ];

        yield 'should with minimum should match' => [
            (new BoolQuery())
                ->addShould($foo)
                ->addShould($bar)
                ->addShould($baz)
                ->setMinimumShouldMatch(self::EXAMPLE_MINIMUM_SHOULD_MATCH),
            [
                'bool' => [
                    'should' => [
                        ['foo' => 'foo'],
                        ['bar' => 'bar'],
                        ['baz' => 'baz'],
                    ],
                    'minimum_should_match' => self::EXAMPLE_MINIMUM_SHOULD_MATCH,
                ],
            ],
        ];

        yield 'must_not' => [
            (new BoolQuery())->addMustNot($foo)->addMustNot($bar)->addMustNot($baz),
            [
                'bool' => [
                    'must_not' => [
                        ['foo' => 'foo'],
                        ['bar' => 'bar'],
                        ['baz' => 'baz'],
                    ],
                ],
            ],
        ];

        return;
    }

    private function createDummyQuery(array $value): Query
    {
        $query = $this->createMock(Query::class);
        $query->method('toArray')->willReturn($value);

        return $query;
    }
}

class_alias(BoolQueryTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\BoolQueryTest');
