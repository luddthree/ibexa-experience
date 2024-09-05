<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\UI\AssetGroup;

use Ibexa\Bundle\ProductCatalog\UI\AssetGroup\AssetGroupCollectionFactory;
use Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroup;
use Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\AssetGroupCollection;
use Ibexa\Bundle\ProductCatalog\UI\AssetGroup\Values\Tag;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterDispatcherInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Asset\Asset;
use Ibexa\ProductCatalog\Local\Repository\Values\Asset\AssetCollection;
use PHPUnit\Framework\TestCase;

final class AssetGroupCollectionFactoryTest extends TestCase
{
    private AssetGroupCollectionFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new AssetGroupCollectionFactory(
            $this->createAttributeDefinitionService(),
            $this->createValueFormatter()
        );
    }

    public function testGroupingEmptyAssetCollection(): void
    {
        self::assertEquals(
            new AssetGroupCollection([
                new AssetGroup(
                    [],
                    new AssetCollection([])
                ),
            ]),
            $this->factory->createFromAssetCollection([])
        );
    }

    public function testGroupingAssetsWithDisjointTags(): void
    {
        $assetA = new Asset($this->createMock(Content::class), 'a', 'asset://a', ['foo' => 'foo']);
        $assetB = new Asset($this->createMock(Content::class), 'b', 'asset://b', ['foo' => 'bar']);
        $assetC = new Asset($this->createMock(Content::class), 'c', 'asset://c', ['foo' => 'baz']);

        self::assertEquals(
            new AssetGroupCollection([
                new AssetGroup(
                    [],
                    new AssetCollection([])
                ),
                new AssetGroup(
                    [
                        new Tag('foo', 'foo', 'FOO', 'FOO'),
                    ],
                    new AssetCollection([$assetA])
                ),
                new AssetGroup(
                    [
                        new Tag('foo', 'bar', 'FOO', 'BAR'),
                    ],
                    new AssetCollection([$assetB])
                ),
                new AssetGroup(
                    [
                        new Tag('foo', 'baz', 'FOO', 'BAZ'),
                    ],
                    new AssetCollection([$assetC])
                ),
            ]),
            $this->factory->createFromAssetCollection([$assetA, $assetB, $assetC])
        );
    }

    public function testCreateFromAssetsWithCustomTags(): void
    {
        $assetA = new Asset($this->createMock(Content::class), 'a', 'asset://a', ['main' => null]);
        $assetB = new Asset($this->createMock(Content::class), 'b', 'asset://b', ['landscape' => null]);
        $assetC = new Asset($this->createMock(Content::class), 'c', 'asset://c', ['detail' => null]);

        self::assertEquals(
            new AssetGroupCollection([
                new AssetGroup(
                    [],
                    new AssetCollection([$assetA, $assetB, $assetC])
                ),
            ]),
            $this->factory->createFromAssetCollection([$assetA, $assetB, $assetC])
        );
    }

    public function testGroupingAssetsWithIntersectingTags(): void
    {
        $assetA = new Asset($this->createMock(Content::class), 'a', 'asset://a', ['foo' => 'foo']);
        $assetB = new Asset($this->createMock(Content::class), 'b', 'asset://b', ['foo' => 'foo']);
        $assetC = new Asset($this->createMock(Content::class), 'c', 'asset://c', ['foo' => 'foo']);

        self::assertEquals(
            new AssetGroupCollection([
                new AssetGroup(
                    [],
                    new AssetCollection([])
                ),
                new AssetGroup(
                    [
                        new Tag('foo', 'foo', 'FOO', 'FOO'),
                    ],
                    new AssetCollection([$assetA, $assetB, $assetC])
                ),
            ]),
            $this->factory->createFromAssetCollection([$assetA, $assetB, $assetC])
        );
    }

    public function testGroupingAssetsWithMultipleTags(): void
    {
        $a = new Asset($this->createMock(Content::class), 'a', 'asset://a', ['foo' => 'foo', 'bar' => 'bar', 'custom' => null]);
        $b = new Asset($this->createMock(Content::class), 'b', 'asset://b', ['foo' => 'foo', 'baz' => 'baz']);
        $c = new Asset($this->createMock(Content::class), 'c', 'asset://c', ['foo' => 'foo', 'bar' => 'bar']);

        self::assertEquals(
            new AssetGroupCollection([
                new AssetGroup(
                    [],
                    new AssetCollection([])
                ),
                new AssetGroup(
                    [
                        new Tag('foo', 'foo', 'FOO', 'FOO'),
                        new Tag('bar', 'bar', 'BAR', 'BAR'),
                    ],
                    new AssetCollection([$a, $c])
                ),
                new AssetGroup(
                    [
                        new Tag('foo', 'foo', 'FOO', 'FOO'),
                        new Tag('baz', 'baz', 'BAZ', 'BAZ'),
                    ],
                    new AssetCollection([$b])
                ),
            ]),
            $this->factory->createFromAssetCollection([$a, $b, $c])
        );
    }

    private function createAttributeDefinitionService(): AttributeDefinitionServiceInterface
    {
        $attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $attributeDefinitionService
            ->method('getAttributeDefinition')
            ->willReturnCallback(function (string $identifier): AttributeDefinitionInterface {
                $definition = $this->createMock(AttributeDefinitionInterface::class);
                $definition->method('getName')->willReturn(strtoupper($identifier));

                return $definition;
            });

        return $attributeDefinitionService;
    }

    private function createValueFormatter(): ValueFormatterDispatcherInterface
    {
        $formatter = $this->createMock(ValueFormatterDispatcherInterface::class);
        $formatter
            ->method('formatValue')
            ->willReturnCallback(static function (AttributeInterface $attribute): string {
                return strtoupper($attribute->getValue());
            });

        return $formatter;
    }
}
