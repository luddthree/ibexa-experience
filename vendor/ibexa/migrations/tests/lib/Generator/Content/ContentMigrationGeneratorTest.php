<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\Content;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\Generator\Content\ContentMigrationGenerator;
use Ibexa\Migration\Generator\CriterionFactoryInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Migration\Generator\Content\ContentMigrationGenerator
 */
final class ContentMigrationGeneratorTest extends TestCase
{
    private const CHUNK_SIZE = 1;

    /** @var \Ibexa\Migration\Generator\Content\ContentMigrationGenerator */
    private $generator;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService|\PHPUnit\Framework\MockObject\MockObject */
    private $searchService;

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $contentStepFactory;

    /** @var \Ibexa\Migration\ValueObject\Step\StepInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $step;

    /** @var \Ibexa\Migration\Generator\Mode */
    private $mode;

    protected function setUp(): void
    {
        $this->mode = new Mode('create');
        $this->step = $this->createMock(StepInterface::class);
        $this->searchService = $this->createMock(SearchService::class);
        $this->contentStepFactory = $this->createMock(StepFactoryInterface::class);
        $criterionFactory = $this->createMock(CriterionFactoryInterface::class);

        $this->generator = new ContentMigrationGenerator(
            $this->searchService,
            $this->contentStepFactory,
            $criterionFactory,
            'content',
            self::CHUNK_SIZE
        );
    }

    public function testGenerateReturnsAllResultsInChunks(): void
    {
        $contentForCall0 = new Content([
            'contentType' => new ContentType([
                'identifier' => 'landing_page',
            ]),
        ]);
        $contentForCall1 = new Content([
            'contentType' => new ContentType([
                'identifier' => 'article',
            ]),
        ]);

        $matcher = self::exactly(3);
        $searchResultFactory = function (Query $query) use ($matcher, $contentForCall0, $contentForCall1): SearchResult {
            self::assertEquals(self::CHUNK_SIZE, $query->limit, sprintf(
                'Invalid limit in call #%s findContent',
                $matcher->getInvocationCount(),
            ));

            switch ($matcher->getInvocationCount()) {
                case 1:
                    self::assertSame(0, $query->offset);

                    return $this->createSearchResultsWithContent($contentForCall0);
                case 2:
                    self::assertSame(1, $query->offset);

                    return $this->createSearchResultsWithContent($contentForCall1);
                case 3:
                    self::assertSame(2, $query->offset);

                    return $this->createEmptySearchResults();
                default:
                    return $this->createEmptySearchResults();
            }
        };
        $this->searchService
            ->expects($matcher)
            ->method('findContent')
            ->willReturnCallback($searchResultFactory);

        $matcher = self::exactly(2);
        $stepFactory = function (Content $content, Mode $mode) use ($matcher, $contentForCall0, $contentForCall1): StepInterface {
            self::assertSame($this->mode, $mode);

            switch ($matcher->getInvocationCount()) {
                case 1:
                    self::assertSame($contentForCall0, $content);
                    break;
                case 2:
                    self::assertSame($contentForCall1, $content);
                    break;
            }

            return $this->step;
        };
        $this->contentStepFactory
            ->expects($matcher)
            ->method('create')
            ->willReturnCallback($stepFactory);

        /** @var \Traversable<\Ibexa\Migration\ValueObject\Step\StepInterface> $results */
        $results = $this->generator->generate(
            $this->mode,
            [
                'value' => ['*'],
                'match-property' => null,
            ]
        );

        $resultsAsArray = iterator_to_array($results, false);
        Assert::assertCount(2, $resultsAsArray);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult<\Ibexa\Core\Repository\Values\Content\Content>
     */
    private function createSearchResultsWithContent(Content $content): SearchResult
    {
        return new SearchResult([
            'totalCount' => 1,
            'searchHits' => [
                new SearchHit([
                    'valueObject' => $content,
                ]),
            ],
        ]);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult<\Ibexa\Core\Repository\Values\Content\Content>
     */
    private function createEmptySearchResults(): SearchResult
    {
        return new SearchResult([
            'totalCount' => 0,
            'searchHits' => [],
        ]);
    }
}

class_alias(ContentMigrationGeneratorTest::class, 'Ibexa\Platform\Tests\Migration\Generator\Content\ContentMigrationGeneratorTest');
