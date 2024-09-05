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
use Ibexa\ProductCatalog\NameSchema\SelectionStrategy;

final class SelectionStrategyTest extends AbstractStrategyTest
{
    private SelectionStrategy $selectionStrategy;

    protected function setUp(): void
    {
        $this->selectionStrategy = new SelectionStrategy();
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
            $this->selectionStrategy->supports($attributeDefinition, $value)
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
            $this->selectionStrategy->resolve(
                $attributeDefinition,
                $value,
                $language
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

        $attributeDefinition = $this->getAttributeDefinitionSelection();
        yield [true, $attributeDefinition, 'foo'];
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
        $attributeDefinition = $this->getAttributeDefinitionSelection();
        yield ['Foo GB', $attributeDefinition, 'foo', self::ENG_GB];
        yield ['Foo PL', $attributeDefinition, 'foo', self::POL_PL];
        yield ['bar', $attributeDefinition, 'bar', self::POL_PL];
    }

    private function getAttributeDefinitionSelection(): AttributeDefinition
    {
        $attributeType = new AttributeType($this->getTranslator(), 'selection');

        return $this->getAttributeDefinition('bar', 'Bar', $attributeType, [
            'choices' => [
                [
                    'value' => 'foo',
                    'label' => [self::ENG_GB => 'Foo GB', self::POL_PL => 'Foo PL'],
                ],
                [
                    'value' => 'bar',
                    'label' => [self::ENG_GB => 'Bar GB'],
                ],
            ],
        ]);
    }
}
