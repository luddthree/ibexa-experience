<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\QueryDSL;

use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RawQuery;
use PHPUnit\Framework\TestCase;

final class RawQueryTest extends TestCase
{
    private const EXAMPLE_RAW_VALUE = [
        'match_all' => [
            'boost' => 2.0,
        ],
    ];

    public function testToArray(): void
    {
        $this->assertEquals(
            self::EXAMPLE_RAW_VALUE,
            (new RawQuery(self::EXAMPLE_RAW_VALUE))->toArray()
        );
    }
}

class_alias(RawQueryTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\QueryDSL\RawQueryTest');
