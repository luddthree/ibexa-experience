<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsQuery;
use PHPUnit\Framework\TestCase;

final class TermsQueryTest extends TestCase
{
    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(TermsQuery $query, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $query->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        yield 'constructor' => [
            new TermsQuery('foo', ['a', 'b', 'c']),
            [
                'terms' => [
                    'foo' => ['a', 'b', 'c'],
                ],
            ],
        ];

        yield 'setters' => [
            (new TermsQuery())->withField('foo')->withValue(['a', 'b', 'c']),
            [
                'terms' => [
                    'foo' => ['a', 'b', 'c'],
                ],
            ],
        ];
    }
}

class_alias(TermsQueryTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\TermsQueryTest');
