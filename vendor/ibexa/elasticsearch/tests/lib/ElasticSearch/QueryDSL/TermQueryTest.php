<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;
use PHPUnit\Framework\TestCase;

final class TermQueryTest extends TestCase
{
    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(TermQuery $query, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $query->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        yield 'constructor' => [
            new TermQuery('foo', 'bar'),
            [
                'term' => [
                    'foo' => 'bar',
                ],
            ],
        ];

        yield 'setters' => [
            (new TermQuery())->withField('foo')->withValue('bar'),
            [
                'term' => [
                    'foo' => 'bar',
                ],
            ],
        ];
    }
}

class_alias(TermQueryTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\TermQueryTest');
