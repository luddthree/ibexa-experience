<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\NumericValueValidator;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use PHPUnit\Framework\TestCase;

final class NumericValueValidatorTest extends TestCase
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
        $validator = new NumericValueValidator();

        $actualErrors = $validator->validateValue($attributeDefinition, $value);

        self::assertEquals($expectedErrors, $actualErrors);
    }

    /**
     * @return iterable<string,array{
     *  \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface,
     *  ?int,
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

        yield 'value < min' => [
            $this->createAttributeDefinitionWithOptions([
                'min' => 10,
            ]),
            0,
            [
                new ValueValidationError(null, 'Value should be greater or equal than %min%', ['%min%' => 10]),
            ],
        ];

        yield 'value > max' => [
            $this->createAttributeDefinitionWithOptions([
                'max' => 10,
            ]),
            100,
            [
                new ValueValidationError(null, 'Value should be lower or equal than %max%', ['%max%' => 10]),
            ],
        ];

        yield 'valid' => [
            $this->createAttributeDefinitionWithOptions([
                'min' => 10,
                'max' => 1000,
            ]),
            666,
            [/* No errors */],
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
