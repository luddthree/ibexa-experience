<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\NullValueFormatter;
use PHPUnit\Framework\TestCase;

final class NullValueFormatterTest extends TestCase
{
    /**
     * @param array<string, mixed> $parameters
     *
     * @dataProvider dataProviderForTestFormatValue
     */
    public function testFormatValue(AttributeInterface $attribute, array $parameters, ?string $expectedValue): void
    {
        $formatter = new NullValueFormatter();

        self::assertEquals(
            $expectedValue,
            $formatter->formatValue($attribute, $parameters)
        );
    }

    /**
     * @return iterable<string,array{AttributeInterface,array<string,mixed>,?string}>
     */
    public function dataProviderForTestFormatValue(): iterable
    {
        yield 'null' => [
            $this->createAttributeWithValue(null),
            [],
            null,
        ];

        yield 'integer' => [
            $this->createAttributeWithValue(1),
            [],
            '1',
        ];

        yield 'string' => [
            $this->createAttributeWithValue('hello'),
            [],
            'hello',
        ];
    }

    /**
     * @param mixed $value
     */
    private function createAttributeWithValue($value): AttributeInterface
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getValue')->willReturn($value);

        return $attribute;
    }
}
