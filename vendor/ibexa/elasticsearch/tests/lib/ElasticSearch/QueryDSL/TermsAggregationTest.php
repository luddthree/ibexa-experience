<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsAggregation;
use PHPUnit\Framework\TestCase;

final class TermsAggregationTest extends TestCase
{
    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(TermsAggregation $query, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $query->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        yield 'constructor' => [
            new TermsAggregation('foo'),
            [
                'terms' => [
                    'field' => 'foo',
                ],
            ],
        ];

        yield 'constructor (all args)' => [
            new TermsAggregation('foo', 10, 3),
            [
                'terms' => [
                    'field' => 'foo',
                    'size' => 10,
                    'min_doc_count' => 3,
                ],
            ],
        ];

        yield 'setters' => [
            (new TermsAggregation('foo'))->withSize(10)->withMinDocCount(3),
            [
                'terms' => [
                    'field' => 'foo',
                    'size' => 10,
                    'min_doc_count' => 3,
                ],
            ],
        ];
    }
}

class_alias(TermsAggregationTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\TermsAggregationTest');
