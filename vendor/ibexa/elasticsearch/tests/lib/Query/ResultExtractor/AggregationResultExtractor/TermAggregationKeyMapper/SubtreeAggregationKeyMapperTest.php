<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Location\SubtreeTermAggregation;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\SubtreeAggregationKeyMapper;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class SubtreeAggregationKeyMapperTest extends TestCase
{
    private const EXAMPLE_PATH_STRING = '/1/2/54/';

    /** @var \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper|\PHPUnit\Framework\MockObject\MockObject */
    private $locationAggregationKeyMapper;

    protected function setUp(): void
    {
        $this->locationAggregationKeyMapper = $this->createMock(TermAggregationKeyMapper::class);
    }

    public function testMap(): void
    {
        $input = ['1', '2', '54', '55', '56', '57'];

        $exceptedResult = $this->createExpectedLocations([54, 55, 56, 57]);

        $aggregation = new SubtreeTermAggregation('example', self::EXAMPLE_PATH_STRING);
        $languageFilter = MockUtils::createEmptyLanguageFilter();

        $this->locationAggregationKeyMapper
            ->method('map')
            ->with($aggregation, $languageFilter, ['54', '55', '56', '57'])
            ->willReturn($exceptedResult);

        $mapper = new SubtreeAggregationKeyMapper($this->locationAggregationKeyMapper);

        $this->assertEquals(
            $exceptedResult,
            $mapper->map(
                $aggregation,
                $languageFilter,
                $input,
            )
        );
    }

    private function createExpectedLocations(iterable $locationIds): array
    {
        $locations = [];
        foreach ($locationIds as $locationId) {
            $locationId = (int)$locationId;

            $location = $this->createMock(Location::class);
            $location->method('__get')->with('id')->willReturn($locationId);

            $locations[$locationId] = $location;
        }

        return $locations;
    }
}

class_alias(SubtreeAggregationKeyMapperTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\SubtreeAggregationKeyMapperTest');
