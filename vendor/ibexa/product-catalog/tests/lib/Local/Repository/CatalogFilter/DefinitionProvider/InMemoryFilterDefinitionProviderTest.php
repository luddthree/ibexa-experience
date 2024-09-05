<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\InMemoryFilterDefinitionProvider;
use PHPUnit\Framework\TestCase;

final class InMemoryFilterDefinitionProviderTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->configResolver
            ->method('getParameter')
            ->with('product_catalog.default_filters')
            ->willReturn(['foo', 'bar', 'foo']);
    }

    public function testHasFilterDefinition(): void
    {
        $provider = new InMemoryFilterDefinitionProvider(
            [
                'existing' => $this->createMock(FilterDefinitionInterface::class),
            ],
            $this->configResolver
        );

        self::assertTrue($provider->hasFilterDefinition('existing'));
        self::assertFalse($provider->hasFilterDefinition('non-existing'));
    }

    public function testGetFilterDefinition(): void
    {
        $expectedFilterDefinition = $this->createMock(FilterDefinitionInterface::class);

        $provider = new InMemoryFilterDefinitionProvider(
            [
                'example' => $expectedFilterDefinition,
            ],
            $this->configResolver
        );

        self::assertSame($expectedFilterDefinition, $provider->getFilterDefinition('example'));
    }

    public function testGetFilterDefinitionThrowsInvalidArgumentException(): void
    {
        $message = "Argument 'identifier' is invalid: Could not find Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface with 'non-existing' identifier";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $provider = new InMemoryFilterDefinitionProvider([/* empty */], $this->configResolver);
        $provider->getFilterDefinition('non-existing');
    }

    public function testGetFilterDefinitions(): void
    {
        $foo = $this->createMock(FilterDefinitionInterface::class);
        $bar = $this->createMock(FilterDefinitionInterface::class);
        $baz = $this->createMock(FilterDefinitionInterface::class);

        $provider = new InMemoryFilterDefinitionProvider(
            [
                'foo' => $foo,
                'bar' => $bar,
                'baz' => $baz,
            ],
            $this->configResolver
        );

        self::assertEquals(
            [
                'foo' => $foo,
                'bar' => $bar,
                'baz' => $baz,
            ],
            $provider->getFilterDefinitions()
        );
    }

    public function testGetDefaultFilterIdentifiers(): void
    {
        $provider = new InMemoryFilterDefinitionProvider(
            [],
            $this->configResolver
        );

        self::assertSame(['foo', 'bar'], $provider->getDefaultFilterIdentifiers());
    }
}
