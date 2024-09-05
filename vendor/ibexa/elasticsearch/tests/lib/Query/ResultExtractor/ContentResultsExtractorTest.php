<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor;
use Ibexa\Elasticsearch\Query\ResultExtractor\ContentResultsExtractor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class ContentResultsExtractorTest extends TestCase
{
    public function testExtract(): void
    {
        $data = [
            [54, 'eng-GB', 1.0],
            [55, 'pol-GB', 2.0],
            [56, 'ger-DE', 3.0],
        ];

        $contentHandler = $this->createMock(ContentHandler::class);
        $contentHandler
            ->method('loadContentInfo')
            ->willReturnCallback(static function (int $id): ContentInfo {
                return new ContentInfo(['id' => $id]);
            });

        $extractor = new ContentResultsExtractor(
            $contentHandler,
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

    public function testExtractSkipMissingContentItems(): void
    {
        $inputData = $this->createInputData([
            [54, 'eng-GB', 1.0],
            [55, 'pol-GB', 2.0],
            [56, 'ger-DE', 3.0],
        ]);

        $contentHandler = $this->createMock(ContentHandler::class);
        $contentHandler
            ->method('loadContentInfo')
            ->willReturnCallback(function (int $id): ContentInfo {
                if ($id % 2 == 1) {
                    throw $this->createMock(NotFoundException::class);
                }

                return new ContentInfo(['id' => $id]);
            });

        $extractor = new ContentResultsExtractor(
            $contentHandler,
            $this->createMock(FacetResultExtractor::class),
            $this->createMock(AggregationResultExtractor::class),
            true
        );

        $actualSearchResults = $extractor->extract(
            MockUtils::createEmptyQueryContext(),
            $inputData
        );

        $expectedSearchResults = $this->createExpectedSearchResults([
            [54, 'eng-GB', 1.0],
            // Skip content with id = 55,
            [56, 'ger-DE', 3.0],
        ]);

        $this->assertEquals($expectedSearchResults, $actualSearchResults);
    }

    public function testExtractThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);

        $contentHandler = $this->createMock(ContentHandler::class);
        $contentHandler->method('loadContentInfo')->willThrowException($this->createMock(NotFoundException::class));

        $inputData = $this->createInputData([
            [54, 'eng-GB', 1.0],
            [55, 'pol-GB', 2.0],
            [56, 'ger-DE', 3.0],
        ]);

        $extractor = new ContentResultsExtractor(
            $contentHandler,
            $this->createMock(FacetResultExtractor::class),
            $this->createMock(AggregationResultExtractor::class),
            false
        );

        $extractor->extract(MockUtils::createEmptyQueryContext(), $inputData);
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
            list($contentId, $matchedTranslation, $score) = array_pad($item, 3, null);

            $data['hits']['hits'][] = [
                '_source' => [
                    ContentResultsExtractor::CONTENT_ID_FIELD => $contentId,
                    ContentResultsExtractor::MATCHED_TRANSLATION_FIELD => $matchedTranslation,
                ],
                '_score' => $score,
            ];

            ++$data['hits']['total']['value'];
        }

        return $data;
    }

    private function createExpectedSearchResults(iterable $items): SearchResult
    {
        $searchResults = new SearchResult();
        $searchResults->totalCount = 0;

        foreach ($items as $item) {
            list($contentId, $matchedTranslation, $score) = array_pad($item, 3, null);

            $searchHit = new SearchHit();
            $searchHit->matchedTranslation = $matchedTranslation;
            $searchHit->valueObject = new ContentInfo(['id' => $contentId]);
            $searchHit->score = $score;

            $searchResults->searchHits[] = $searchHit;

            ++$searchResults->totalCount;
        }

        return $searchResults;
    }
}

class_alias(ContentResultsExtractorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\ContentResultsExtractorTest');
