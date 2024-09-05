<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\BooleanAggregationKeyMapper;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class BooleanAggregationKeyMapperTest extends TestCase
{
    public function testMap(): void
    {
        $mapper = new BooleanAggregationKeyMapper();

        $this->assertEquals(
            [
                0 => false,
                1 => true,
            ],
            $mapper->map(
                $this->createMock(Aggregation::class),
                MockUtils::createEmptyLanguageFilter(),
                [0, 1]
            )
        );
    }
}

class_alias(BooleanAggregationKeyMapperTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\BooleanAggregationKeyMapperTest');
