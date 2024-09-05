<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\Matcher\TaxonomyEntryBased;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\View;
use PHPUnit\Framework\TestCase;

abstract class AbstractMatcherTest extends TestCase
{
    private const EXAMPLE_CONTENT_ID = 21;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected TaxonomyServiceInterface $taxonomyService;

    protected function setUp(): void
    {
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
    }

    /**
     * @return iterable<string, array{mixed,TaxonomyEntry,bool}>
     */
    abstract public function dataProviderForTestMatch(): iterable;

    /**
     * @param mixed $matchingConfig
     *
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatchContentInfo(
        $matchingConfig,
        TaxonomyEntry $taxonomyEntry,
        bool $expectedResult
    ): void {
        $matcher = $this->createMatcher($matchingConfig);
        $contentInfo = $this->createContentInfo(true, $taxonomyEntry);

        self::assertEquals($expectedResult, $matcher->matchContentInfo($contentInfo));
    }

    /**
     * @param mixed $matchingConfig
     *
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatchLocation(
        $matchingConfig,
        TaxonomyEntry $taxonomyEntry,
        bool $expectedResult
    ): void {
        $matcher = $this->createMatcher($matchingConfig);
        $location = $this->createLocation(true, $taxonomyEntry);

        self::assertEquals($expectedResult, $matcher->matchLocation($location));
    }

    /**
     * @param mixed $matchingConfig
     *
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatch(
        $matchingConfig,
        TaxonomyEntry $taxonomyEntry,
        bool $expectedResult
    ): void {
        $matcher = $this->createMatcher($matchingConfig);
        $view = $this->createContentValueView(true, $taxonomyEntry);

        self::assertEquals($expectedResult, $matcher->match($view));
    }

    public function testMatchContentInfoWithNonTaxonomyEntry(): void
    {
        self::assertFalse($this->createMatcher()->matchContentInfo($this->createContentInfo(false)));
    }

    public function testMatchLocationWithNonTaxonomyEntry(): void
    {
        self::assertFalse($this->createMatcher()->matchLocation($this->createLocation(false)));
    }

    public function testMatchNonTaxonomyEntry(): void
    {
        self::assertFalse($this->createMatcher()->match($this->createContentValueView(false)));
    }

    public function testMatchNonContentValueView(): void
    {
        self::assertFalse($this->createMatcher()->match($this->createMock(View::class)));
    }

    /**
     * @param mixed $matchingConfig
     */
    abstract protected function createMatcher($matchingConfig = null): MatcherInterface;

    /**
     * @return \Ibexa\Core\MVC\Symfony\View\View&\Ibexa\Core\MVC\Symfony\View\ContentValueView
     */
    protected function createContentValueView(
        bool $withTaxonomyEntry,
        ?TaxonomyEntry $taxonomyEntry = null
    ): View {
        $content = $this->createContent($withTaxonomyEntry, $taxonomyEntry);

        return new class($content) extends BaseView implements ContentValueView {
            private Content $content;

            public function __construct(Content $content)
            {
                parent::__construct();

                $this->content = $content;
            }

            public function getContent(): Content
            {
                return $this->content;
            }
        };
    }

    protected function createLocation(
        bool $withTaxonomyEntry,
        ?TaxonomyEntry $taxonomyEntry = null
    ): Location {
        $location = $this->createMock(Location::class);
        $location
            ->method('getContent')
            ->willReturn($this->createContent($withTaxonomyEntry, $taxonomyEntry));

        return $location;
    }

    protected function createContent(
        bool $withTaxonomyEntry,
        ?TaxonomyEntry $taxonomyEntry = null
    ): Content {
        $content = $this->createMock(Content::class);
        $content->method('__get')->with('id')->willReturn(self::EXAMPLE_CONTENT_ID);

        if ($withTaxonomyEntry) {
            $this->taxonomyService
                ->method('loadEntryByContentId')
                ->with(self::EXAMPLE_CONTENT_ID)
                ->willReturn($taxonomyEntry ?? $this->createMock(TaxonomyEntry::class));
        } else {
            $this->taxonomyService
                ->method('loadEntryByContentId')
                ->willThrowException($this->createMock(NotFoundException::class));
        }

        return $content;
    }

    protected function createContentInfo(
        bool $withTaxonomyEntry,
        ?TaxonomyEntry $taxonomyEntry = null
    ): ContentInfo {
        $contentInfo = $this->createMock(ContentInfo::class);
        $contentInfo
            ->method('getMainLocation')
            ->willReturn($this->createLocation($withTaxonomyEntry, $taxonomyEntry));

        return $contentInfo;
    }
}
