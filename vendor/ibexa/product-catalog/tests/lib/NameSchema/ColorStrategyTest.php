<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\NameSchema;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\AttributeType;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\NameSchema\ColorStrategy;

final class ColorStrategyTest extends AbstractStrategyTest
{
    public const COLOR_HEX = '#eb4034';
    public const COLOR_STRING = 'eb4034';

    private ColorStrategy $colorStrategy;

    protected function setUp(): void
    {
        $this->colorStrategy = new ColorStrategy();
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
            $this->colorStrategy->supports($attributeDefinition, $value)
        );
    }

    /**
     * @dataProvider dataProviderForTestResolve
     *
     * @param mixed $value
     */
    public function testResolve(
        string $expectedValue,
        AttributeDefinitionInterface $attributeDefinition,
        $value,
        string $language
    ): void {
        self::assertEquals(
            $expectedValue,
            $this->colorStrategy->resolve(
                $attributeDefinition,
                $value,
                $language
            )
        );
    }

    /**
     * @return iterable<array{
     *     bool,
     *     \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface,
     *     mixed
     * }>
     */
    public function dataProviderForTestSupports(): iterable
    {
        $attributeDefinition = $this->getAttributeDefinitionFoo();
        $value = false;

        yield [
            false,
            $attributeDefinition,
            $value,
        ];

        $attributeDefinition = $this->getAttributeDefinitionColor();
        $value = self::COLOR_HEX;

        yield [true, $attributeDefinition, $value];
    }

    /**
     * @return iterable<array{
     *     string,
     *     \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface,
     *     mixed,
     *     string
     * }>
     */
    public function dataProviderForTestResolve(): iterable
    {
        $attributeDefinition = $this->getAttributeDefinitionColor();
        yield [self::COLOR_STRING, $attributeDefinition, self::COLOR_HEX, self::ENG_GB];
        yield [self::COLOR_STRING, $attributeDefinition, self::COLOR_HEX, self::POL_PL];
    }

    private function getAttributeDefinitionColor(): AttributeDefinition
    {
        $attributeType = new AttributeType($this->getTranslator(), 'color');

        return $this->getAttributeDefinition('bar', 'Bar', $attributeType);
    }
}
