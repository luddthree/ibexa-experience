<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RangeQuery;
use PHPUnit\Framework\TestCase;

final class RangeQueryTest extends TestCase
{
    private const EXAMPLE_FIELD = 'foo';
    private const EXAMPLE_VALUE_A = 100;
    private const EXAMPLE_VALUE_B = 1000;

    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(RangeQuery $query, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $query->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        yield from $this->dateProviderForSingleArgOperator();

        yield 'constructor with between' => [
            new RangeQuery(
                self::EXAMPLE_FIELD,
                Operator::BETWEEN,
                [
                    self::EXAMPLE_VALUE_A,
                    self::EXAMPLE_VALUE_B,
                ]
            ),
            [
                'range' => [
                    self::EXAMPLE_FIELD => [
                        'gte' => self::EXAMPLE_VALUE_A,
                        'lte' => self::EXAMPLE_VALUE_B,
                    ],
                ],
            ],
        ];

        yield 'setters with between' => [
            (new RangeQuery())
                ->withField(self::EXAMPLE_FIELD)
                ->withOperator(Operator::BETWEEN)
                ->withRange(self::EXAMPLE_VALUE_A, self::EXAMPLE_VALUE_B),
            [
                'range' => [
                    self::EXAMPLE_FIELD => [
                        'gte' => self::EXAMPLE_VALUE_A,
                        'lte' => self::EXAMPLE_VALUE_B,
                    ],
                ],
            ],
        ];
    }

    private function dateProviderForSingleArgOperator(): iterable
    {
        $conversionMap = [
            Operator::GT => 'gt',
            Operator::GTE => 'gte',
            Operator::LT => 'lt',
            Operator::LTE => 'lte',
        ];

        foreach ($conversionMap as $eZOperator => $eSOperator) {
            yield 'constructor with ' . $eZOperator => [
                new RangeQuery(
                    self::EXAMPLE_FIELD,
                    $eZOperator,
                    [self::EXAMPLE_VALUE_A]
                ),
                [
                    'range' => [
                        self::EXAMPLE_FIELD => [
                            $eSOperator => self::EXAMPLE_VALUE_A,
                        ],
                    ],
                ],
            ];

            yield 'setters with ' . $eZOperator => [
                (new RangeQuery())
                    ->withField(self::EXAMPLE_FIELD)
                    ->withOperator($eZOperator)
                    ->withRange(self::EXAMPLE_VALUE_A),
                [
                    'range' => [
                        self::EXAMPLE_FIELD => [
                            $eSOperator => self::EXAMPLE_VALUE_A,
                        ],
                    ],
                ],
            ];
        }
    }
}

class_alias(RangeQueryTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\RangeQueryTest');
