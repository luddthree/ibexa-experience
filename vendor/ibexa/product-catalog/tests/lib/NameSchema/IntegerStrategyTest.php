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
use Ibexa\ProductCatalog\NameSchema\IntegerStrategy;

final class IntegerStrategyTest extends AbstractStrategyTest
{
    private IntegerStrategy $integerStrategy;

    protected function setUp(): void
    {
        $this->integerStrategy = new IntegerStrategy();
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
            $this->integerStrategy->supports($attributeDefinition, $value)
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
            $this->integerStrategy->resolve(
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

        $attributeDefinition = $this->getAttributeDefinitionInteger();
        $value = '666';

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
        $attributeDefinition = $this->getAttributeDefinitionInteger();
        yield ['666', $attributeDefinition, '666', self::ENG_GB];
        yield ['666', $attributeDefinition, '666', self::POL_PL];
    }

    private function getAttributeDefinitionInteger(): AttributeDefinition
    {
        $attributeType = new AttributeType($this->getTranslator(), 'integer');

        return $this->getAttributeDefinition('bar', 'Bar', $attributeType);
    }
}
