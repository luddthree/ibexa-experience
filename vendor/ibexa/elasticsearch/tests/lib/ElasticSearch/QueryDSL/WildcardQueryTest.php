<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\WildcardQuery;
use PHPUnit\Framework\TestCase;

final class WildcardQueryTest extends TestCase
{
    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(WildcardQuery $query, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $query->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        yield 'constructor' => [
            new WildcardQuery('foo', 'bar*'),
            [
                'wildcard' => [
                    'foo' => [
                        'value' => 'bar*',
                    ],
                ],
            ],
        ];

        yield 'setters' => [
            (new WildcardQuery())->withField('foo')->withValue('bar*'),
            [
                'wildcard' => [
                    'foo' => [
                        'value' => 'bar*',
                    ],
                ],
            ],
        ];
    }
}

class_alias(WildcardQueryTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\WildcardQueryTest');
