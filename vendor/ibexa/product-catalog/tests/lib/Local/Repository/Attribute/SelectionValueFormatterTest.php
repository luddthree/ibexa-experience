<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace lib\Local\Repository\Attribute;

use Ibexa\Contracts\Core\Options\OptionsBag;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\SelectionValueFormatter;
use PHPUnit\Framework\TestCase;

final class SelectionValueFormatterTest extends TestCase
{
    /**
     * @param array<string, mixed> $parameters
     *
     * @dataProvider dataProviderForTestFormatValue
     */
    public function testFormatValue(AttributeInterface $attribute, array $parameters, ?string $expectedValue): void
    {
        $languageResolve = $this->createMock(LanguageResolver::class);
        $languageResolve
            ->method('getPrioritizedLanguages')
            ->willReturnCallback(static function (?array $languages) {
                if ($languages !== null) {
                    return $languages;
                }

                return ['pol-PL', 'eng-GB'];
            });

        $formatter = new SelectionValueFormatter($languageResolve);

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
            $this->createAttribute(null),
            [],
            null,
        ];

        yield 'default' => [
            $this->createAttribute('value', [
                [
                    'value' => 'value',
                    'label' => [
                        'eng-GB' => 'Human readable label',
                    ],
                ],
            ]),
            [],
            'Human readable label',
        ];

        yield 'forced language' => [
            $this->createAttribute('value', [
                [
                    'value' => 'value',
                    'label' => [
                        'eng-GB' => 'Human readable label',
                        'fre-FR' => 'Human readable label (FR)',
                    ],
                ],
            ]),
            ['languages' => ['fre-FR']],
            'Human readable label (FR)',
        ];

        yield 'missing label' => [
            $this->createAttribute('value', [
                [
                    'value' => 'value',
                    'label' => [/* No labels */],
                ],
            ]),
            [],
            'value',
        ];
    }

    /**
     * @param array<mixed>|null $choices
     */
    private function createAttribute(?string $value, ?array $choices = null): AttributeInterface
    {
        $options = $this->createMock(OptionsBag::class);
        $options->method('get')->with('choices')->willReturn($choices);

        $definition = $this->createMock(AttributeDefinitionInterface::class);
        $definition->method('getOptions')->willReturn($options);

        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getAttributeDefinition')->willReturn($definition);
        $attribute->method('getValue')->willReturn($value);

        return $attribute;
    }
}
