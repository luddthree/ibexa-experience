<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\FacetResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor;
use Ibexa\Elasticsearch\Query\FacetBuilderVisitor\FilteredFacetVisitorDecorator;
use Ibexa\Elasticsearch\Query\ResultExtractor\FacetResultExtractor\FilteredFacetResultExtractorDecorator;
use PHPUnit\Framework\TestCase;

final class FilteredFacetResultExtractorDecoratorTest extends TestCase
{
    private const EXAMPLE_UNWRAPPED_DATA = [
        'buckets' => [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
        ],
    ];

    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetResultExtractor|\PHPUnit\Framework\MockObject\MockObject */
    private $innerResultExtractor;

    /** @var \Ibexa\Elasticsearch\Query\ResultExtractor\FacetResultExtractor\FilteredFacetResultExtractorDecorator */
    private $resultExtractor;

    protected function setUp(): void
    {
        $this->innerResultExtractor = $this->createMock(FacetResultExtractor::class);
        $this->resultExtractor = new FilteredFacetResultExtractorDecorator($this->innerResultExtractor);
    }

    public function testSupports(): void
    {
        $facetBuilder = $this->createMock(FacetBuilder::class);

        $this->innerResultExtractor
            ->expects($this->once())
            ->method('supports')
            ->with($facetBuilder)
            ->willReturn(true);

        $this->assertTrue($this->resultExtractor->supports($facetBuilder));
    }

    public function testExtract(): void
    {
        $facetBuilder = new TestFacetBuilder();
        $facetBuilder->filter = $this->createMock(Criterion::class);

        $expectedFacet = new TestFacet();

        $data = [
            [
                'buckets' => [
                    'foo' => rand(),
                    'bar' => rand(),
                    'baz' => rand(),
                ],
            ],
        ];

        $wrappedData = [
            FilteredFacetVisitorDecorator::INNER_AGGREGATION_KEY => $data,
        ];

        $this->innerResultExtractor
            ->expects($this->once())
            ->method('extract')
            ->with($facetBuilder, $data)
            ->willReturn($expectedFacet);

        $this->assertEquals($expectedFacet, $this->resultExtractor->extract($facetBuilder, $wrappedData));
    }

    public function testExtractNonFilteredFacet(): void
    {
        $facetBuilder = new TestFacetBuilder();
        $facetBuilder->global = false;

        $expectedFacet = new TestFacet();

        $this->innerResultExtractor
            ->expects($this->once())
            ->method('extract')
            ->with($facetBuilder, self::EXAMPLE_UNWRAPPED_DATA)
            ->willReturn($expectedFacet);

        $this->assertEquals(
            $expectedFacet,
            $this->resultExtractor->extract(
                $facetBuilder,
                self::EXAMPLE_UNWRAPPED_DATA
            )
        );
    }
}

class_alias(FilteredFacetResultExtractorDecoratorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\FacetResultExtractor\FilteredFacetResultExtractorDecoratorTest');
