<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor;

use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\QueryContext;
use Ibexa\Elasticsearch\Query\ResultExtractor\LocationResultsExtractor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class LocationResultsExtractorTest extends TestCase
{
    public function testExtract(): void
    {
        $data = [
            [54, 'eng-GB', 1.0],
            [55, 'pol-GB', 2.0],
            [56, 'ger-DE', 3.0],
        ];

        $locationHandler = $this->createMock(LocationHandler::class);
        $locationHandler
            ->method('load')
            ->willReturnCallback(static function (int $id): Location {
                return new Location(['id' => $id]);
            });

        $extractor = new LocationResultsExtractor(
            $locationHandler,
            $this->createMock(FacetResultExtractor::class),
            $this->createMock(AggregationResultExtractor::class)
        );

        $this->assertEquals(
            $this->createExpectedSearchResults($data),
            $extractor->extract(
                MockUtils::createEmptyQueryContext(),
                $this->createInputData($data)
            )
        );
    }

    public function testExtractSkipMissingLocations(): void
    {
        $inputData = $this->createInputData([
            [54, 'eng-GB', 1.0],
            [55, 'pol-GB', 2.0],
            [56, 'ger-DE', 3.0],
        ]);

        $locationHandler = $this->createMock(LocationHandler::class);
        $locationHandler
            ->method('load')
            ->willReturnCallback(function (int $id): Location {
                if ($id % 2 == 1) {
                    throw $this->createMock(NotFoundException::class);
                }

                return new Location(['id' => $id]);
            });

        $extractor = new LocationResultsExtractor(
            $locationHandler,
            $this->createMock(FacetResultExtractor::class),
            $this->createMock(AggregationResultExtractor::class),
            true
        );

        $actualSearchResults = $extractor->extract(MockUtils::createEmptyQueryContext(), $inputData);

        $expectedSearchResults = $this->createExpectedSearchResults([
            [54, 'eng-GB', 1.0],
            // Skip location with id = 55,
            [56, 'ger-DE', 3.0],
        ]);

        $this->assertEquals($expectedSearchResults, $actualSearchResults);
    }

    public function testExtractThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);

        $locationHandler = $this->createMock(LocationHandler::class);
        $locationHandler->method('load')->willThrowException($this->createMock(NotFoundException::class));

        $inputData = $this->createInputData([
            [54, 'eng-GB', 1.0],
            [55, 'pol-GB', 2.0],
            [56, 'ger-DE', 3.0],
        ]);

        $extractor = new LocationResultsExtractor(
            $locationHandler,
            $this->createMock(FacetResultExtractor::class),
            $this->createMock(AggregationResultExtractor::class),
            false
        );

        $extractor->extract(MockUtils::createEmptyQueryContext(), $inputData);
    }

    private function createExpectedSearchResults(iterable $items): SearchResult
    {
        $searchResults = new SearchResult();
        $searchResults->totalCount = 0;

        foreach ($items as $item) {
            list($locationId, $matchedTranslation, $score) = array_pad($item, 3, null);

            $searchHit = new SearchHit();
            $searchHit->matchedTranslation = $matchedTranslation;
            $searchHit->valueObject = new Location(['id' => $locationId]);
            $searchHit->score = $score;

            $searchResults->searchHits[] = $searchHit;

            ++$searchResults->totalCount;
        }

        return $searchResults;
    }

    private function createInputData(iterable $generator): array
    {
        $data = [
            'hits' => [
                'total' => [
                    'value' => 0,
                ],
                'hits' => [],
            ],
        ];

        foreach ($generator as $item) {
            list($locationId, $matchedTranslation, $score) = array_pad($item, 3, null);

            $data['hits']['hits'][] = [
                '_source' => [
                    LocationResultsExtractor::LOCATION_ID_FIELD => $locationId,
                    LocationResultsExtractor::MATCHED_TRANSLATION_FIELD => $matchedTranslation,
                ],
                '_score' => $score,
            ];

            ++$data['hits']['total']['value'];
        }

        return $data;
    }

    private function createQueryContext(): QueryContext
    {
        return new QueryContext(new Query(), new LanguageFilter([], false, false));
    }
}

class_alias(LocationResultsExtractorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\LocationResultsExtractorTest');
