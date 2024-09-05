<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper;
use Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationResultExtractor;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;

final class TermAggregationResultExtractorTest extends AbstractAggregationResultExtractorTest
{
    /** @var \Ibexa\Elasticsearch\Query\ResultExtractor\AggregationResultExtractor\TermAggregationKeyMapper|\PHPUnit\Framework\MockObject\MockObject */
    private $keyMapper;

    protected function setUp(): void
    {
        $this->keyMapper = $this->createMock(TermAggregationKeyMapper::class);
        $this->keyMapper
            ->method('map')
            ->willReturnCallback(static function (
                Aggregation $aggregation,
                LanguageFilter $languageFilter,
                array $keys
            ): array {
                return array_combine($keys, array_map('strtoupper', $keys));
            });

        $this->extractor = $this->createExtractor();
    }

    protected function createExtractor(): AggregationResultExtractor
    {
        return new TermAggregationResultExtractor(
            AbstractTermAggregation::class,
            $this->keyMapper
        );
    }

    public function dataProviderForTestSupports(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        yield 'true' => [
            $this->createMock(AbstractTermAggregation::class),
            $emptyLanguageFilter,
            true,
        ];

        yield 'false' => [
            $this->createMock(Aggregation::class),
            $emptyLanguageFilter,
            false,
        ];
    }

    public function dataProviderForTestExtract(): iterable
    {
        $emptyLanguageFilter = MockUtils::createEmptyLanguageFilter();

        $aggregation = $this->createMock(AbstractTermAggregation::class);
        $aggregation->method('getName')->willReturn(self::EXAMPLE_AGGREGATION_NAME);

        yield 'defaults' => [
            $aggregation,
            $emptyLanguageFilter,
            [
                'buckets' => [],
            ],
            new TermAggregationResult(self::EXAMPLE_AGGREGATION_NAME, []),
        ];

        yield 'typical' => [
            $aggregation,
            $emptyLanguageFilter,
            [
                'buckets' => [
                    [
                        'key' => 'foo',
                        'doc_count' => 10,
                    ],
                    [
                        'key' => 'bar',
                        'doc_count' => 100,
                    ],
                    [
                        'key' => 'baz',
                        'doc_count' => 1000,
                    ],
                ],
            ],
            new TermAggregationResult(
                self::EXAMPLE_AGGREGATION_NAME,
                [
                    new TermAggregationResultEntry('FOO', 10),
                    new TermAggregationResultEntry('BAR', 100),
                    new TermAggregationResultEntry('BAZ', 1000),
                ]
            ),
        ];
    }
}

class_alias(TermAggregationResultExtractorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\AggregationResultExtractor\TermAggregationResultExtractorTest');
