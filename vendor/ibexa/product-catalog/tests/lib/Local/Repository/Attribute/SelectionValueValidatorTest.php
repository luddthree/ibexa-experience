<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\SelectionValueValidator;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use PHPUnit\Framework\TestCase;

final class SelectionValueValidatorTest extends TestCase
{
    /**
     * @param mixed $value
     * @param \Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError[] $expectedErrors
     *
     * @dataProvider dataProviderForValidate
     */
    public function testValidate(
        AttributeDefinitionInterface $attributeDefinition,
        $value,
        array $expectedErrors
    ): void {
        $validator = new SelectionValueValidator();

        $actualErrors = $validator->validateValue($attributeDefinition, $value);

        self::assertEquals($expectedErrors, $actualErrors);
    }

    /**
     * @return iterable<string,array{
     *  \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface,
     *  mixed,
     *  \Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError[]
     * }>
     */
    public function dataProviderForValidate(): iterable
    {
        yield 'empty value' => [
            $this->createAttributeDefinitionWithOptions([]),
            null,
            [/* No errors */],
        ];

        yield 'existing choice' => [
            $this->createAttributeDefinitionWithOptions([
                'choices' => [
                    ['value' => 'foo', 'label' => 'Foo'],
                    ['value' => 'bar', 'label' => 'Bar'],
                    ['value' => 'baz', 'label' => 'Baz'],
                ],
            ]),
            'foo',
            [/* No errors */],
        ];

        yield 'non-existing choice' => [
            $this->createAttributeDefinitionWithOptions([
                'choices' => [
                    ['value' => 'foo', 'label' => 'Foo'],
                    ['value' => 'bar', 'label' => 'Bar'],
                    ['value' => 'baz', 'label' => 'Baz'],
                ],
            ]),
            'foobaz',
            [
                new ValueValidationError(null, 'Undefined selection value: "%value%"', ['%value%' => 'foobaz']),
            ],
        ];
    }

    /**
     * @param array<string,mixed> $options
     */
    private function createAttributeDefinitionWithOptions(array $options): AttributeDefinitionInterface
    {
        $definition = $this->createMock(AttributeDefinitionInterface::class);
        $definition->method('getOptions')->willReturn(new AttributeDefinitionOptions($options));

        return $definition;
    }
}
