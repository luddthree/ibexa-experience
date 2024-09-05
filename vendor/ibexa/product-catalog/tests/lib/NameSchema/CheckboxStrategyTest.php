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
use Ibexa\ProductCatalog\NameSchema\CheckboxStrategy;

final class CheckboxStrategyTest extends AbstractStrategyTest
{
    private CheckboxStrategy $checkboxStrategy;

    protected function setUp(): void
    {
        $this->checkboxStrategy = new CheckboxStrategy();
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
            $this->checkboxStrategy->supports($attributeDefinition, $value)
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
            $this->checkboxStrategy->resolve(
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

        $attributeDefinition = $this->getAttributeDefinitionCheckbox();
        $value = false;

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
        $attributeDefinition = $this->getAttributeDefinitionFoo();
        $value = 'Foo';
        yield ['', $attributeDefinition, $value, self::ENG_GB];
        yield ['', $attributeDefinition, $value, self::POL_PL];

        $attributeDefinition = $this->getAttributeDefinitionCheckbox();
        $value = true;
        yield ['Bar', $attributeDefinition, $value, self::ENG_GB];
        yield ['Bar', $attributeDefinition, $value, self::POL_PL];

        $value = false;
        yield ['', $attributeDefinition, $value, self::ENG_GB];
        yield ['', $attributeDefinition, $value, self::POL_PL];
    }

    private function getAttributeDefinitionCheckbox(): AttributeDefinition
    {
        $attributeType = new AttributeType($this->getTranslator(), 'checkbox');

        return $this->getAttributeDefinition('bar', 'Bar', $attributeType);
    }
}
