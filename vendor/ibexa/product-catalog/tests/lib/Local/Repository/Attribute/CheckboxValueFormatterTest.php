<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\CheckboxValueFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CheckboxValueFormatterTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestFormatValue
     */
    public function testFormatValue(AttributeInterface $attribute, ?string $expectedValue): void
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $formatter = new CheckboxValueFormatter($translator);

        self::assertEquals($expectedValue, $formatter->formatValue($attribute));
    }

    /**
     * @return iterable<string,array{AttributeInterface,?string}>
     */
    public function dataProviderForTestFormatValue(): iterable
    {
        yield 'null' => [
            $this->createAttributeWithValue(null),
            null,
        ];

        yield 'true' => [
            $this->createAttributeWithValue(true),
            'checkbox.value.true',
        ];

        yield 'false' => [
            $this->createAttributeWithValue(false),
            'checkbox.value.false',
        ];
    }

    private function createAttributeWithValue(?bool $value): AttributeInterface
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getValue')->willReturn($value);

        return $attribute;
    }
}
