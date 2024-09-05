<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Dashboard\ViewMatcher;

use Ibexa\Bundle\Dashboard\ViewMatcher\IsDashboardMatcher;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Dashboard\ViewMatcher\IsDashboardMatcher
 */
final class IsDashboardMatcherTest extends TestCase
{
    use ConfigResolverMockTrait;
    use ContentItemOfContentTypeMockTrait;

    private IsDashboardMatcher $viewMatcher;

    protected function setUp(): void
    {
        $this->viewMatcher = new IsDashboardMatcher(
            new IsDashboardContentType($this->mockConfigResolver())
        );
    }

    /**
     * @return iterable<string, array{
     *     callable(\Ibexa\Tests\Bundle\Dashboard\ViewMatcher\IsDashboardMatcherTest): \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo,
     *     boolean
     * }>
     */
    public static function getDataForTestMatchContentInfo(): iterable
    {
        yield 'true for Dashboard content' => [
            static fn (self $self): ContentInfo => $self->mockContentInfoOfDashboardType(),
            true,
        ];
        yield 'false for non-Dashboard content' => [
            static fn (self $self): ContentInfo => $self->mockContentInfoOfContentType('foo'),
            false,
        ];
    }

    /**
     * @dataProvider getDataForTestMatchContentInfo
     *
     * @phpstan-param callable(\Ibexa\Tests\Bundle\Dashboard\ViewMatcher\IsDashboardMatcherTest): \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfoBuilder
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testMatchContentInfo(callable $contentInfoBuilder, bool $expectedResult): void
    {
        $this->viewMatcher->setMatchingConfig(true);
        self::assertSame($expectedResult, $this->viewMatcher->matchContentInfo($contentInfoBuilder($this)));
    }

    /**
     * @return iterable<string, array{
     *     callable(\Ibexa\Tests\Bundle\Dashboard\ViewMatcher\IsDashboardMatcherTest): \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *     boolean
     * }>
     */
    public static function getDataForTestMatchLocation(): iterable
    {
        yield 'true for Location with Dashboard content' => [
            static fn (self $self): Location => $self->mockLocationOfContentItemOfDashboardType(),
            true,
        ];
        yield 'false for Location with non-Dashboard content' => [
            static fn (self $self): Location => $self->mockLocationOfContentItemOfContentType('foo'),
            false,
        ];
    }

    /**
     * @dataProvider getDataForTestMatchLocation
     *
     * @phpstan-param callable(\Ibexa\Tests\Bundle\Dashboard\ViewMatcher\IsDashboardMatcherTest): \Ibexa\Contracts\Core\Repository\Values\Content\Location $locationBuilder
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testMatchLocation(callable $locationBuilder, bool $expectedResult): void
    {
        $this->viewMatcher->setMatchingConfig(true);

        self::assertSame($expectedResult, $this->viewMatcher->matchLocation($locationBuilder($this)));
    }

    /**
     * @phpstan-return iterable<string,
     *     array{
     *      boolean,
     *      callable(\Ibexa\Tests\Bundle\Dashboard\ViewMatcher\IsDashboardMatcherTest): \Ibexa\Core\MVC\Symfony\View\View,
     *      boolean
     * }>
     */
    public static function getDataForTestMatch(): iterable
    {
        $regularViewBuilder = static fn (self $self): View => $self->createMock(View::class);
        yield 'Always false for non-ContentValueView views with match config = true' => [
            true, // match config
            $regularViewBuilder,
            false, // expected result
        ];
        yield 'Always false for non-ContentValueView views with match config = false' => [
            false, // match config
            $regularViewBuilder,
            false, // expected result
        ];

        // --

        $dashboardContentValueViewBuilder = static function (self $self): View {
            $contentView = new ContentView();
            $contentView->setContent($self->mockContentItemOfDashboardType());

            return $contentView;
        };
        yield 'True for ContentValueView view with Dashboard content item with match config = true' => [
            true, // match config
            $dashboardContentValueViewBuilder,
            true, // expected result
        ];
        yield 'False for ContentValueView view with Dashboard content item with match config = false' => [
            false, // match config
            $dashboardContentValueViewBuilder,
            false, // expected result
        ];

        // --

        $nonDashboardContentValueViewBuilder = static function (self $self): View {
            $contentView = new ContentView();
            $contentView->setContent($self->mockContentItemOfContentType('foo'));

            return $contentView;
        };
        yield 'False for ContentValueView view with non-Dashboard content item with match config = true' => [
            true, // match config
            $nonDashboardContentValueViewBuilder,
            false, // expected result
        ];
        yield 'True for ContentValueView view with non-Dashboard content item with match config = false' => [
            false, // match config
            $nonDashboardContentValueViewBuilder,
            true, // expected result
        ];
    }

    /**
     * @dataProvider getDataForTestMatch
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testMatch(bool $matchConfig, callable $viewBuilder, bool $expectedResult): void
    {
        $this->viewMatcher->setMatchingConfig($matchConfig);

        self::assertSame($expectedResult, $this->viewMatcher->match($viewBuilder($this)));
    }

    /**
     * @return iterable<string, array{bool}>
     */
    public static function getMatchConfig(): iterable
    {
        yield 'IsDashboard: true' => [true];
        yield 'IsDashboard: false' => [false];
    }

    /**
     * @dataProvider getMatchConfig
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testSetMatchingConfig(bool $configValue): void
    {
        $this->expectNotToPerformAssertions();

        $this->viewMatcher->setMatchingConfig($configValue);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testSetMatchingConfigThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument \'$matchingConfig\' is invalid: IsDashboard View Matcher config should be true or false'
        );
        $this->viewMatcher->setMatchingConfig([true]);
    }
}
