<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\NameSchema;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Measurement\NameSchema\MeasurementSimpleStrategy;
use Symfony\Contracts\Translation\TranslatorInterface;

final class MeasurementSimpleStrategyTest extends AbstractStrategyTest
{
    private MeasurementSimpleStrategy $measurementSimpleStrategy;

    protected function setUp(): void
    {
        $this->measurementSimpleStrategy = new MeasurementSimpleStrategy();
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
            $this->measurementSimpleStrategy->supports($attributeDefinition, $value)
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
            $this->measurementSimpleStrategy->resolve(
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
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $attributeGroup = $this->getAttributeGroup();
        $attributeDefinition = $this->getAttributeDefinitionRange($translatorMock, $attributeGroup);
        $value = $this->getSimpleValue();

        yield [
            false,
            $attributeDefinition,
            $value,
        ];

        $attributeDefinition = $this->getAttributeDefinitionSimple($translatorMock, $attributeGroup);
        $value = $this->getRangeValue();

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
        $translatorMock = $this->createMock(TranslatorInterface::class);
        $attributeGroup = $this->getAttributeGroup();
        $attributeDefinition = $this->getAttributeDefinitionRange($translatorMock, $attributeGroup);
        $value = $this->getSimpleValue();

        yield [
            '111meter',
            $attributeDefinition,
            $value,
            self::ENG_GB,
        ];

        yield [
            '111meter',
            $attributeDefinition,
            $value,
            self::POL_PL,
        ];

        $attributeDefinition = $this->getAttributeDefinitionSimple($translatorMock, $attributeGroup);
        $value = $this->getRangeValue();

        yield ['', $attributeDefinition, $value, self::ENG_GB];
        yield ['', $attributeDefinition, $value, self::POL_PL];
    }
}
