<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Bundle\ProductCatalog\Form\Attribute\SelectionValueChoiceLoader;
use Ibexa\Contracts\Core\Options\OptionsBag;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use PHPUnit\Framework\TestCase;

final class SelectionValueChoiceLoaderTest extends TestCase
{
    /**
     * @param array<array{ label: array<string,string>, value: string }> $choices
     * @param string[] $languages
     * @param array<string,?string> $expectedChoiceList
     *
     * @dataProvider dataProviderForTestGetChoiceList
     */
    public function testGetChoiceList(array $choices, array $languages, array $expectedChoiceList): void
    {
        $loader = new SelectionValueChoiceLoader(
            $this->createLanguageResolver($languages),
            $this->createAttributeDefinition($choices)
        );

        self::assertEquals($expectedChoiceList, $loader->getChoiceList());
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public function dataProviderForTestGetChoiceList(): iterable
    {
        yield 'translation' => [
            [
                [
                    'value' => 'foo',
                    'label' => [
                        'eng-GB' => 'Foo (eng-GB)',
                        'pol-PL' => 'Foo (pol-PL)',
                    ],
                ],
            ],
            ['pol-PL', 'eng-GB'],
            [
                'Foo (pol-PL)' => 'foo',
            ],
        ];

        yield 'translation fallback' => [
            [
                [
                    'value' => 'bar',
                    'label' => [
                        'eng-GB' => 'Bar (eng-GB)',
                    ],
                ],
            ],
            ['pol-PL', 'eng-GB'],
            [
                'Bar (eng-GB)' => 'bar',
            ],
        ];

        yield 'missing label' => [
            [
                [
                    'value' => 'baz',
                    'label' => [],
                ],
            ],
            ['pol-PL', 'eng-GB'],
            [
                'baz' => 'baz',
            ],
        ];
    }

    /**
     * @param string[] $languages
     */
    private function createLanguageResolver(array $languages): LanguageResolver
    {
        $resolver = $this->createMock(LanguageResolver::class);
        $resolver->method('getPrioritizedLanguages')->willReturn($languages);

        return $resolver;
    }

    /**
     * @param array<array{ label: array<string,string>, value: string }> $choices
     */
    private function createAttributeDefinition(array $choices): AttributeDefinitionInterface
    {
        $options = $this->createMock(OptionsBag::class);
        $options->method('get')->with('choices')->willReturn($choices);

        $definition = $this->createMock(AttributeDefinitionInterface::class);
        $definition->method('getOptions')->willReturn($options);

        return $definition;
    }
}
