<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\NameSchema;

use Ibexa\Contracts\ProductCatalog\NameSchema\NameSchemaStrategyInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\AttributeType;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\NameSchema\NameSchemaStrategy;

final class NameStrategyTest extends AbstractStrategyTest
{
    private NameSchemaStrategy $nameSchemaStrategy;

    protected function setUp(): void
    {
        $strategy1 = $this->createMock(NameSchemaStrategyInterface::class);
        $strategy2 = $this->createMock(NameSchemaStrategyInterface::class);
        $this->nameSchemaStrategy = new NameSchemaStrategy([$strategy1, $strategy2]);
    }

    /**
     * @dataProvider dataProviderForTestSupports
     *
     * @param mixed $value
     */
    public function testSupports(
        bool $expectedValue,
        AttributeDefinitionInterface $attributeDefinition,
        $value
    ): void {
        self::assertEquals(
            $expectedValue,
            $this->nameSchemaStrategy->supports($attributeDefinition, $value)
        );
    }

    public function testResolve(): void
    {
        $strategy1 = $this->createMock(NameSchemaStrategyInterface::class);
        $strategy1->expects(self::never())->method('resolve');
        $strategy1->expects(self::once())->method('supports')->willReturn(false);
        $strategy2 = $this->createMock(NameSchemaStrategyInterface::class);
        $strategy2
            ->expects(self::once())
            ->method('resolve')
            ->willReturnCallback(static function (AttributeDefinitionInterface $attributeDefinition, $value) {
                return $value;
            });
        $strategy2->expects(self::once())->method('supports')->willReturn(true);
        $nameSchemaStrategy = new NameSchemaStrategy([$strategy1, $strategy2]);

        self::assertEquals(
            'Foo',
            $nameSchemaStrategy->resolve(
                $this->getAttributeDefinitionFoo(),
                'Foo',
                'foo'
            )
        );
    }

    public function testNeverResolve(): void
    {
        $strategy1 = $this->createMock(NameSchemaStrategyInterface::class);
        $strategy1->expects(self::never())->method('resolve');
        $strategy1->expects(self::once())->method('supports')->willReturn(false);
        $strategy2 = $this->createMock(NameSchemaStrategyInterface::class);
        $strategy2->expects(self::never())->method('resolve');
        $strategy2->expects(self::once())->method('supports')->willReturn(false);
        $nameSchemaStrategy = new NameSchemaStrategy([$strategy1, $strategy2]);

        self::assertEquals(
            '',
            $nameSchemaStrategy->resolve(
                $this->getAttributeDefinitionFoo(),
                'foo',
                'foo'
            )
        );
    }

    /**
     * @return iterable<array{bool, \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface, mixed}>
     */
    public function dataProviderForTestSupports(): iterable
    {
        $attributeDefinition = $this->getAttributeDefinitionFoo();
        yield [false, $attributeDefinition, false];

        $attributeDefinition = $this->getAttributeDefinitionBar();
        yield [false, $attributeDefinition, true];
    }

    private function getAttributeDefinitionBar(): AttributeDefinition
    {
        $attributeType = new AttributeType($this->getTranslator(), 'bar');

        return $this->getAttributeDefinition('bar', 'Bar', $attributeType);
    }
}
