<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased\IsAvailable;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;

final class IsAvailableTest extends AbstractProductMatcherTest
{
    /** @var \Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductAvailabilityServiceInterface $availabilityService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->availabilityService = $this->createMock(ProductAvailabilityServiceInterface::class);
    }

    /**
     * @param mixed $matchingConfig
     *
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatchContentInfo(
        $matchingConfig,
        ?AvailabilityInterface $availability,
        bool $expectedResult
    ): void {
        $product = $this->createProductWithAvailability($availability);
        $matcher = $this->createMatcher($matchingConfig);
        $contentInfo = $this->createContentInfo(true, $product);

        self::assertEquals($expectedResult, $matcher->matchContentInfo($contentInfo));
    }

    /**
     * @param mixed $matchingConfig
     *
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatchLocation(
        $matchingConfig,
        ?AvailabilityInterface $availability,
        bool $expectedResult
    ): void {
        $product = $this->createProductWithAvailability($availability);
        $matcher = $this->createMatcher($matchingConfig);
        $location = $this->createLocation(true, $product);

        self::assertEquals($expectedResult, $matcher->matchLocation($location));
    }

    /**
     * @param mixed $matchingConfig
     *
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatchProduct(
        $matchingConfig,
        ?AvailabilityInterface $availability,
        bool $expectedResult
    ): void {
        $product = $this->createProductWithAvailability($availability);
        $matcher = $this->createMatcher($matchingConfig);
        $view = $this->createContentValueView(true, $product);

        self::assertEquals($expectedResult, $matcher->match($view));
    }

    /**
     * @return iterable<string,array{bool,?AvailabilityInterface,bool}>
     */
    public function dataProviderForTestMatch(): iterable
    {
        yield 'match' => [
            true,
            $this->createAvailability(true),
            true,
        ];

        yield 'match (negative)' => [
            false,
            $this->createAvailability(false),
            true,
        ];

        yield 'miss' => [
            true,
            $this->createAvailability(false),
            false,
        ];

        yield 'miss (negative)' => [
            false,
            $this->createAvailability(true),
            false,
        ];

        yield 'miss (undefined)' => [
            true,
            null,
            false,
        ];
    }

    private function createProductWithAvailability(
        ?AvailabilityInterface $availability
    ): ProductInterface {
        $product = $this->createMock(ProductInterface::class);

        $this->availabilityService
            ->method('hasAvailability')
            ->with($product)
            ->willReturn($availability !== false);

        if ($availability !== null) {
            $this->availabilityService
                ->method('getAvailability')
                ->with($product)
                ->willReturn($availability);
        }

        return $product;
    }

    private function createAvailability(bool $available): AvailabilityInterface
    {
        $availability = $this->createMock(AvailabilityInterface::class);
        $availability->method('isAvailable')->willReturn($available);

        return $availability;
    }

    protected function createMatcher($matchingConfig = null): MatcherInterface
    {
        $matcher = new IsAvailable($this->productService, $this->availabilityService);
        $matcher->setMatchingConfig($matchingConfig);

        return $matcher;
    }
}
