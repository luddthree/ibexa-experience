<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ColorValueValidator;
use PHPUnit\Framework\TestCase;

final class ColorValueValidatorTest extends TestCase
{
    /**
     * @param mixed $value
     * @param \Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError[] $expectedErrors
     *
     * @dataProvider dataProviderForValidate
     */
    public function testValidate($value, array $expectedErrors): void
    {
        $validator = new ColorValueValidator();

        $actualErrors = $validator->validateValue(
            $this->createMock(AttributeDefinitionInterface::class),
            $value
        );

        self::assertEquals($expectedErrors, $actualErrors);
    }

    /**
     * @return iterable<string,array{
     *  ?mixed,
     *  \Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError[]
     * }>
     */
    public function dataProviderForValidate(): iterable
    {
        yield 'empty value' => [
            null,
            [/* No errors */],
        ];

        yield 'non-string' => [
            10,
            [
                new ValueValidationError(null, 'Expected string, got integer'),
            ],
        ];

        yield 'invalid format' => [
            'invalid',
            [
                new ValueValidationError(null, "Value doesn't match /^#[0-9A-Fa-f]{6}$/ pattern"),
            ],
        ];

        yield 'valid' => [
            '#FF0000',
            [/* No errors */],
        ];
    }
}
