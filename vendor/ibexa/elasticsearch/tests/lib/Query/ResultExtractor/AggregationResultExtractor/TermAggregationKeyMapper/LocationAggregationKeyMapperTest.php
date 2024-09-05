<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\LocationAggregationKeyMapper;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class LocationAggregationKeyMapperTest extends TestCase
{
    private const EXAMPLE_LOCATION_IDS = [51, 52, 53];

    /** @var \Ibexa\Contracts\Core\Repository\LocationService|\PHPUnit\Framework\MockObject\MockObject */
    private $locationService;

    protected function setUp(): void
    {
        $this->locationService = $this->createMock(LocationService::class);
    }

    public function testMap(): void
    {
        $expectedLocations = $this->createExpectedLocations(self::EXAMPLE_LOCATION_IDS);

        $this->locationService
            ->method('loadLocationList')
            ->with(self::EXAMPLE_LOCATION_IDS)
            ->willReturn($expectedLocations);

        $mapper = new LocationAggregationKeyMapper($this->locationService);

        $this->assertEquals(
            array_combine(
                self::EXAMPLE_LOCATION_IDS,
                $expectedLocations
            ),
            $mapper->map(
                $this->createMock(Aggregation::class),
                MockUtils::createEmptyLanguageFilter(),
                self::EXAMPLE_LOCATION_IDS
            )
        );
    }

    private function createExpectedLocations(iterable $ids): array
    {
        $locations = [];
        foreach ($ids as $id) {
            $location = $this->createMock(Location::class);
            $location->method('__get')->with('id')->willReturn($id);

            $locations[] = $location;
        }

        return $locations;
    }
}

class_alias(LocationAggregationKeyMapperTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper\LocationAggregationKeyMapperTest');
