<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\ChainFilterDefinitionProvider;
use PHPUnit\Framework\TestCase;

final class ChainFilterDefinitionProviderTest extends TestCase
{
    public function testHasFilterDefinition(): void
    {
        $chain = new ChainFilterDefinitionProvider([
            $this->createFilterDefinitionProvider([/* Empty */]),
            $this->createFilterDefinitionProvider([
                'existing' => $this->createMock(FilterDefinitionInterface::class),
            ]),
        ]);

        self::assertTrue($chain->hasFilterDefinition('existing'));
        self::assertFalse($chain->hasFilterDefinition('non-existing'));
    }

    public function testGetFilterDefinition(): void
    {
        $expectedFilterDefinitionA = $this->createMock(FilterDefinitionInterface::class);
        $expectedFilterDefinitionB = $this->createMock(FilterDefinitionInterface::class);

        $chain = new ChainFilterDefinitionProvider([
            $this->createFilterDefinitionProvider([
                'foo' => $expectedFilterDefinitionA,
                'bar' => $this->createMock(FilterDefinitionInterface::class),
            ]),
            $this->createFilterDefinitionProvider([
                'baz' => $expectedFilterDefinitionB,
            ]),
        ]);

        self::assertSame($expectedFilterDefinitionA, $chain->getFilterDefinition('foo'));
        self::assertSame($expectedFilterDefinitionB, $chain->getFilterDefinition('baz'));
    }

    public function testGetFilterDefinitionThrowsInvalidArgumentException(): void
    {
        $message = 'Argument \'$identifier\' is invalid: Unknown filter definition with identifier "non-existing"';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        $chain = new ChainFilterDefinitionProvider([
            $this->createFilterDefinitionProvider([
                'foo' => $this->createMock(FilterDefinitionInterface::class),
                'bar' => $this->createMock(FilterDefinitionInterface::class),
            ]),
            $this->createFilterDefinitionProvider([
                'baz' => $this->createMock(FilterDefinitionInterface::class),
            ]),
        ]);

        $chain->getFilterDefinition('non-existing');
    }

    public function testGetFilterDefinitions(): void
    {
        $foo = $this->createMock(FilterDefinitionInterface::class);
        $bar = $this->createMock(FilterDefinitionInterface::class);
        $baz = $this->createMock(FilterDefinitionInterface::class);

        $chain = new ChainFilterDefinitionProvider([
            $this->createFilterDefinitionProvider([
                'foo' => $foo,
                'bar' => $bar,
            ]),
            $this->createFilterDefinitionProvider([
                'baz' => $baz,
            ]),
        ]);

        $expectedFilterDefinitions = [$foo, $baz, $baz];
        foreach ($chain->getFilterDefinitions() as $i => $actualFilterDefinition) {
            self::assertEquals($expectedFilterDefinitions[$i], $actualFilterDefinition);
        }
    }

    public function testGetDefaultFilterIdentifiers(): void
    {
        $chain = new ChainFilterDefinitionProvider([
            $this->createFilterDefinitionProvider([], ['foo', 'bar']),
            $this->createFilterDefinitionProvider([], ['baz']),
            $this->createFilterDefinitionProvider([], ['foo']),
        ]);

        self::assertSame(['foo', 'bar', 'baz'], $chain->getDefaultFilterIdentifiers());
    }

    /**
     * @param array<string, \Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface> $definitions
     * @param string[] $defaultFilterIdentifiers
     */
    private function createFilterDefinitionProvider(
        array $definitions,
        array $defaultFilterIdentifiers = []
    ): FilterDefinitionProviderInterface {
        $provider = $this->createMock(FilterDefinitionProviderInterface::class);

        $provider
            ->method('hasFilterDefinition')
            ->willReturnCallback(
                static fn (string $identifier): bool => array_key_exists($identifier, $definitions)
            );

        $provider
            ->method('getFilterDefinition')
            ->willReturnCallback(
                static fn (string $identifier): FilterDefinitionInterface => $definitions[$identifier]
            );

        $provider->method('getFilterDefinitions')->willReturn(array_values($definitions));

        $provider->method('getDefaultFilterIdentifiers')->willReturn($defaultFilterIdentifiers);

        return $provider;
    }
}
