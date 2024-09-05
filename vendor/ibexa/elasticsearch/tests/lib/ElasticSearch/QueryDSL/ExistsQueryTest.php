<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\ExistsQuery;
use PHPUnit\Framework\TestCase;

final class ExistsQueryTest extends TestCase
{
    /**
     * @dataProvider dataProviderForToArray
     */
    public function testToArray(ExistsQuery $query, array $expectedValue): void
    {
        $this->assertEquals($expectedValue, $query->toArray());
    }

    public function dataProviderForToArray(): iterable
    {
        yield 'constructor' => [
            new ExistsQuery('foo'),
            [
                'exists' => [
                    'field' => 'foo',
                ],
            ],
        ];
    }
}

class_alias(ExistsQueryTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\ExistsQueryTest');
