<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\CatalogFilter;

use Ibexa\Bundle\ProductCatalog\Form\CatalogFilter\FilterFormMapperRegistry;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FilterFormMapperRegistryTest extends TestCase
{
    public function testHasMapperForSupportedFilterDefinition(): void
    {
        $definition = $this->createMock(FilterDefinitionInterface::class);

        $registry = new FilterFormMapperRegistry([
            $this->createMapper($definition, false),
            $this->createMapper($definition, true),
            $this->createMapper($definition, false),
        ]);

        self::assertTrue($registry->hasMapper($definition));
    }

    public function testHasMapperForUnsupportedFilterDefinition(): void
    {
        $definition = $this->createMock(FilterDefinitionInterface::class);

        $registry = new FilterFormMapperRegistry([
            $this->createMapper($definition, false),
            $this->createMapper($definition, false),
            $this->createMapper($definition, false),
        ]);

        self::assertFalse($registry->hasMapper($definition));
    }

    public function testGetMapper(): void
    {
        $definition = $this->createMock(FilterDefinitionInterface::class);

        $foo = $this->createMapper($definition, false);
        $bar = $this->createMapper($definition, true);
        $baz = $this->createMapper($definition, false);

        $registry = new FilterFormMapperRegistry([$foo, $bar, $baz]);

        self::assertSame($bar, $registry->getMapper($definition));
    }

    public function testGetMapperThrowsInvalidArgumentException(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Argument \'$fieldDefinition\' is invalid: Undefined Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface for filter definition with identifier "foo"');

        $definition = $this->createMock(FilterDefinitionInterface::class);
        $definition->method('getIdentifier')->willReturn('foo');

        $registry = new FilterFormMapperRegistry([
            $this->createMapper($definition, false),
            $this->createMapper($definition, false),
            $this->createMapper($definition, false),
        ]);

        $registry->getMapper($definition);
    }

    private function createMapper(FilterDefinitionInterface $definition, bool $supported): FilterFormMapperInterface
    {
        $mapper = $this->createMock(FilterFormMapperInterface::class);
        $mapper->method('supports')->with($definition)->willReturn($supported);

        return $mapper;
    }
}
